<template>
  <k-panel-inside class="k-git-content-view">
    <k-header>Git Content {{ size }}</k-header>

    <section class="k-section" v-if="helpText">
      <k-box :text="helpText" html="true" theme="info" />
    </section>

    <k-section
      v-if="status.files.length"
      :buttons="[
        { text: 'Revert Changes', icon: 'undo', click: revert },
        { text: 'Commit Changes', icon: 'check', click: commit },
      ]"
      label="Uncommitted changes"
    >
      <k-collection
        :items="statusItems"
        help="Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a> on how to interpret the status codes to the right."
      />
    </k-section>

    <k-section
      :buttons="[
        { text: 'Pull', icon: 'download', click: pull },
        { text: 'Push', icon: 'upload', click: push },
      ]"
      label="Remote synchronization"
    >
      <k-box :text="remoteStatus.text" :theme="remoteStatus.theme" />
    </k-section>

    <k-section
      :buttons="[
        { text: 'Create Branch', icon: 'add', click: createBranch },
        { text: 'Switch Branch', icon: 'refresh', click: switchBranch },
      ]"
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
    disableBranchManagement: {
      type: Boolean,
      default: false,
    },
    helpText: {},
  },
  computed: {
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
    revert: async function () {
      this.$dialog("git-content/revert");
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
