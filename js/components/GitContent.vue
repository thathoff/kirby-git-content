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
      <k-collection
        :items="statusItems"
        help="Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a> on how to interpret the status codes to the right."
      />
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
  computed: {
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
    statusItems() {
      const items = [];

      this.status.files.forEach((file) => {
        items.push({
          text: file.filename,
          info: file.code,
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
        },
        {
          key: "commit",
          text: "Commit Changes",
          icon: "check",
          click: this.commit,
          class: "btn-commit",
        },
      ];

      return buttons.filter((button) => this.buttonMap[button.key]);
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
    revert: async function () {
      this.$dialog("git-content/revert");
    },
		reset: async function () {
      this.$dialog("git-content/reset");
    },
    commit: async function () {
      this.$dialog("git-content/commit");
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
