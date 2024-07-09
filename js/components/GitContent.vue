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
                { text: listTypeInfo.label, icon: listTypeInfo.icon, click: toggleList },
                { text: 'Revert all Changes', icon: 'undo', click: revert },
                { text: 'Commit all Changes', icon: 'check', click: commit },
              ]"
            />
          </header>

          <k-collection
            :items="statusItems"
            v-if="listType === 'files'"
            help="Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a>
              on how to interpret the status codes to the right."
          />
          <k-collection
            v-else
            :items="statusPages"
          />
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
  data: function () {
    return {
      listType: 'pages',
    }
  },
  computed: {
    listTypeInfo () {
      if (this.listType === 'pages') {
        return {
          label: 'by Pages',
          icon: 'page',
        }
      }

      return {
        label: 'by Files',
        icon: 'file',
      }
    },
    commitItems () {
      const items = []

      this.log.forEach(commit => {
        const authorName = commit.user?.username || commit.author || 'Unknown'
        const avatar = commit.user?.avatar ? '<img class="git-content-view__avatar" src="'+ commit.user?.avatar + '">' : ''

        items.push({
          text: commit.message + ' <small class="git-content-view__commit-id">' + commit.hash.substr(0, 7) + '</small>',
          info: avatar + authorName + ', ' + this.formatRelative(commit.date),
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
    statusPages () {
      const items = []

      this.status.pages.forEach(page => {
        const infoText = page.changeString

        const title =
          page.pageName + ' <small class="git-content-view__page-id">' +
            page.pageId + '</small>'
        const dialogUrl = 'page=' + encodeURIComponent(page.pageId)
          + '&files=' + encodeURIComponent(JSON.stringify(page.files))

        items.push({
          text: title,
          info: infoText,
          image: page.image,
          link: page.panelUrl,
          options: [
            {
              text: 'Commit Changes',
              icon: 'check',
              click: () => {
                this.$dialog('git-content/commit/?' + dialogUrl, {
                  width: 'large'
                })
              }
            },
            {
              text: 'Revert Changes',
              icon: 'refresh',
              click: () => {
                this.$dialog('git-content/revert?=' + dialogUrl)
              }
            }
          ]
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
    toggleList: function () {
      this.listType = this.listType === 'pages' ? 'files' : 'pages'
    },
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
<style>
.git-content-view__commit-id {
  color: var(--color-gray-500);
  font-family: var(--font-mono);
  font-size: 0.8em;
}

.git-content-view__page-id {
  color: var(--color-gray-500);
  font-size: 0.8em;
}

.git-content-view__avatar {
  border-radius: 50%;
  height: 20px;
  aspect-ratio: 1/1;
  margin-right: 0.5em;
  float: left;
}
</style>
