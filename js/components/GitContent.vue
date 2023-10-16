<template>
  <k-inside>
    <k-view class="k-git-content-view">
      <k-header>Git Content {{ size }}</k-header>

      <section class="k-section" v-if="helpText">
        <k-box :text="helpText" html="true" theme="info" />
      </section>

      <section v-if="status.files.length" class="k-section">
          <header class="k-section-header">
            <k-headline>Uncommitted changes</k-headline>

             <k-button-group
              :buttons="[
                { text: 'Revert Changes', icon: 'undo', click: revert },
                { text: 'Commit Changes', icon: 'check', click: commit },
              ]"
            />
          </header>

          <k-collection :items="statusItems" help="Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a> on how to interpret the status codes to the right." />
      </section>

      <section class="k-section">
        <header class="k-section-header">
          <k-headline>Remote synchronization</k-headline>

          <k-button-group
            :buttons="[
              { text: 'Pull', icon: 'download', click: pull },
              { text: 'Push', icon: 'upload', click: push },
            ]"
          />
        </header>
        <k-box :text="remoteStatus.text" :theme="remoteStatus.theme" />
      </section>

      <section class="k-section">
        <header class="k-section-header">
          <k-headline>Latest {{ log.length }} changes on branch »{{ branch }}«</k-headline>

          <k-button-group
            v-if="!disableBranchManagement"
            :buttons="[
              { text: 'Create Branch', icon: 'add', click: createBranch },
              { text: 'Switch Branch', icon: 'refresh', click: switchBranch },
            ]"
          />
        </header>
        <k-collection :items="commitItems" />
      </section>
    </k-view>
  </k-inside>
</template>
<script>
import formatDistance from 'date-fns/formatDistance'

export default {
  name: 'GitContent',
  props: {
    status: {
      type: Object,
    },
    log: {
      type: Array,
      default: []
    },
    branch: {
      type: String,
      default: ''
    },
    disableBranchManagement: {
      type: Boolean,
      default: false
    },
    helpText: {
    },
  },
  computed: {
    commitItems () {
      const items = []

      this.log.forEach(commit => {
        items.push({
          text: commit.message,
          info: this.formatRelative(commit.date) + ' / ' + commit.hash.substr(0, 7),
          link: false,
        })
      })

      return items
    },
    statusItems () {
      const items = []

      this.status.files.forEach(file => {
        items.push({
          text: file.filename,
          info: file.code,
          link: false,
        })
      })

      return items
    },
    remoteStatus () {
      if (!this.status.hasRemote) {
        return {
          text: 'No remote branch found.',
          theme: 'negative',
        }
      }

      if (this.status.diffFromOrigin === 0) {
        return {
          text: 'Your branch is up to date with origin/' + this.branch,
          theme: 'positive',
        }
      }

      const absDiff = Math.abs(this.status.diffFromOrigin)

      return {
        text: `Your branch is ${this.status.diffFromOrigin > 0 ? "ahead" : "behind"} of origin/${this.branch} by ${absDiff} commit${absDiff !== 1 ? "s" : ""}.`,
        theme: 'notice',
      }
    }
  },
  methods: {
    pull: async function () {
      await panel.app.$api.post('/git-content/pull')
      this.$reload()
    },
    push: async function () {
      await panel.app.$api.post('/git-content/push')
      this.$reload()
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
    formatRelative (date) {
      return formatDistance(new Date(date), new Date(), {
        addSuffix: true
      })
    }
  }
}
</script>
