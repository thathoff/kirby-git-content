<template>
  <k-panel-inside class="k-git-content-view">
    <k-header>Git Content {{ size }}</k-header>

    <section class="k-section" v-if="helpText">
      <k-box :text="helpText" html="true" theme="info" />
    </section>

    <k-section
      v-if="status.files.length"
      :buttons="changeButtons"
      label="Uncommitted changes"
    >
      <ul class="k-git-content-status-list">
        <li
          v-for="file in status.files"
          :key="file.filename"
          class="k-git-content-status-item"
        >
          <k-checkbox-input
            :checked="isFileSelected(file.filename)"
            :value="isFileSelected(file.filename)"
            @input="toggleFile(file.filename)"
          />
          <span class="k-git-content-status-filename">{{ file.filename }}</span>
          <span class="k-git-content-status-code">{{ file.code }}</span>
        </li>
      </ul>
      <p class="k-git-content-status-help">
        Refer to the <a target="_blank" href="https://git-scm.com/docs/git-status#_short_format">Git documentation</a> on how to interpret the status codes to the right.
      </p>
    </k-section>

    <k-section
      :buttons="remoteButtons"
      label="Remote synchronization"
    >
      <k-box :text="remoteStatus.text" :theme="remoteStatus.theme" />
    </k-section>

    <k-section
      :buttons="branchButtons"
      :label="`Latest ${log.length} changes on branch »${branch}«`"
    >
      <k-collection :items="commitItems" />
    </k-section>
  </k-panel-inside>
</template>
<script>
import formatDistance from "date-fns/formatDistance";

export default {
  name: "GitContent",
  props: {
    status: {
      type: Object,
    },
    log: {
      type: Array,
      default: [],
    },
    branch: {
      type: String,
      default: "",
    },
    hasIndexLock: {
      type: Boolean,
      default: false,
    },
    disableBranchManagement: {
      type: Boolean,
      default: false,
    },
    helpText: {},
    buttons: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      selectedFiles: [],
    };
  },
  watch: {
    "status.files": {
      immediate: true,
      handler() {
        this.selectedFiles = [];
      },
    },
  },
  computed: {
    allFilesSelected() {
      return (
        this.status.files.length > 0
        && this.selectedFiles.length === this.status.files.length
      );
    },
    differsFromRemote() {
      return this.status.diffFromOrigin !== 0;
    },
    buttonMap() {
      return {
        revert: true,
        commit: true,
        pull: true,
        push: true,
        createBranch: true,
        switchBranch: true,
        ...this.buttons,
      };
    },
    commitItems() {
      const items = [];

      this.log.forEach((commit) => {
        items.push({
          text: commit.message,
          info:
            this.formatRelative(commit.date)
            + " / "
            + commit.author
            + " / "
            + commit.hash.substr(0, 7),
          link: false,
        });
      });

      return items;
    },
    changeButtons() {
      const buttons = [
        {
          key: "revert",
          text: "Revert Changes",
          icon: "undo",
          click: this.revert,
          class: "btn-revert",
          disabled: this.selectedFiles.length === 0,
        },
        {
          key: "commit",
          text: "Commit Changes",
          icon: "check",
          click: this.commit,
          class: "btn-commit",
          disabled: this.selectedFiles.length === 0,
        },
      ].filter((button) => this.buttonMap[button.key]);

      buttons.unshift({
        key: "selectAll",
        text: this.allFilesSelected ? "Deselect All" : "Select All",
        icon: this.allFilesSelected ? "circle-nested" : "circle",
        click: this.toggleSelectAll,
        class: "btn-select-all",
        // render as a plain link instead of the group's filled button style
        variant: null,
      });

      return buttons;
    },
    remoteButtons() {
      const buttons = [
        {
        	key: "fetch",
        	text: "Fetch",
        	icon: "refresh",
        	click: this.fetch,
        	class: "btn-fetch",
        },
        {
          key: "pull",
          text: "Pull",
          icon: "download",
          click: this.pull,
          class: "btn-pull",
        },
        {
          key: "push",
          text: "Push",
          icon: "upload",
          click: this.push,
          class: "btn-push",
        },
      ];

      if (this.differsFromRemote) {
        buttons.unshift({
					key: "reset",
					text: "Reset",
					icon: "undo",
					click: this.reset,
					class: "btn-reset",
				});
      }

      const filteredButtons = buttons.filter((button) => this.buttonMap[button.key]);

      if (this.hasIndexLock) {
        filteredButtons.unshift({
          key: "removeIndexLock",
          text: "Remove Index Lock",
          icon: "unlock",
          click: this.removeIndexLock,
          class: "btn-remove-index-lock",
        });
      }

      return filteredButtons;
    },
    branchButtons() {
      if (this.disableBranchManagement) {
        return [];
      }

      const buttons = [
        {
          key: "createBranch",
          text: "Create Branch",
          icon: "add",
          click: this.createBranch,
          class: "btn-create",
        },
        {
          key: "switchBranch",
          text: "Switch Branch",
          icon: "split",
          click: this.switchBranch,
          class: "btn-switch",
        },
      ];

      return buttons.filter((button) => this.buttonMap[button.key]);
    },
    remoteStatus() {
      if (!this.status.hasRemote) {
        return {
          text: "No remote branch found.",
          theme: "negative",
        };
      }

      if (this.status.diffFromOrigin === 0) {
        return {
          text: "Your branch is up to date with origin/" + this.branch,
          theme: "positive",
        };
      }

      const absDiff = Math.abs(this.status.diffFromOrigin);

      return {
        text: `Your branch is ${
          this.status.diffFromOrigin > 0 ? "ahead" : "behind"
        } of origin/${this.branch} by ${absDiff} commit${
          absDiff !== 1 ? "s" : ""
        }.`,
        theme: "notice",
      };
    },
  },
  methods: {
    pull: async function () {
      await panel.app.$api.post("/git-content/pull");
      this.$reload();
    },
    push: async function () {
      await panel.app.$api.post("/git-content/push");
      this.$reload();
    },
    fetch: async function () {
      await panel.app.$api.post("/git-content/fetch");
      this.$reload();
    },
    removeIndexLock: async function () {
      await panel.app.$api.post("/git-content/remove-index-lock");
      this.$reload();
    },
    toggleSelectAll() {
      this.selectedFiles = this.allFilesSelected
        ? []
        : this.status.files.map((file) => file.filename);
    },
    isFileSelected(filename) {
      return this.selectedFiles.includes(filename);
    },
    toggleFile(filename) {
      this.selectedFiles = this.isFileSelected(filename)
        ? this.selectedFiles.filter((selected) => selected !== filename)
        : [...this.selectedFiles, filename];
    },
    revert: function () {
      const files = this.selectedFiles;
      if (files.length === 0) {
        return;
      }

      panel.dialog.open({
        component: "k-remove-dialog",
        props: {
          text: "Are you sure you want to revert the selected changes?<br><br>⚠️ This cannot be undone.",
          submitButton: "Revert changes",
          icon: "undo",
        },
        on: {
          submit: async () => {
            await panel.app.$api.post("/git-content/revert", { files });
            panel.dialog.close();
            this.$reload();
          },
        },
      });
    },
		reset: async function () {
      this.$dialog("git-content/reset");
    },
    commit: function () {
      const files = this.selectedFiles;
      if (files.length === 0) {
        return;
      }

      panel.dialog.open({
        component: "k-form-dialog",
        props: {
          fields: {
            title: {
              label: "Title",
              type: "text",
              counter: true,
              maxlength: 72,
              required: true,
            },
            description: {
              label: "Description",
              type: "textarea",
              buttons: false,
              required: false,
            },
          },
          size: "large",
        },
        on: {
          submit: async (values) => {
            await panel.app.$api.post("/git-content/commit", {
              title: values.title,
              description: values.description,
              files,
            });
            panel.dialog.close();
            this.$reload();
          },
        },
      });
    },
    switchBranch: async function () {
      this.$dialog("git-content/branch");
    },
    createBranch: async function () {
      this.$dialog("git-content/create-branch");
    },
    formatRelative(date) {
      return formatDistance(new Date(date), new Date(), {
        addSuffix: true,
      });
    },
  },
};
</script>
<style scoped>
.k-git-content-view ::v-deep .btn-select-all {
  margin-inline-end: var(--spacing-1);
  padding-inline-end: var(--spacing-3);
  border-inline-end: 1px solid var(--color-border);
  border-radius: 0;
}
.k-git-content-status-list {
  list-style: none;
  margin: 0;
  padding: 0;
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
  overflow: hidden;
}
.k-git-content-status-item {
  display: flex;
  align-items: center;
  gap: var(--spacing-3);
  padding: var(--spacing-2) var(--spacing-3);
}
.k-git-content-status-item ::v-deep .k-choice-input-icon,
.k-git-content-status-item ::v-deep .k-choice-input input {
  top: 0;
}
.k-git-content-status-item + .k-git-content-status-item {
  border-top: 1px solid var(--color-border);
}
.k-git-content-status-filename {
  flex-grow: 1;
  overflow-wrap: anywhere;
}
.k-git-content-status-code {
  font-family: var(--font-mono);
  color: var(--color-text-dimmed);
}
.k-git-content-status-help {
  margin-top: var(--spacing-2);
  color: var(--color-text-dimmed);
  font-size: var(--text-sm);
}
</style>
