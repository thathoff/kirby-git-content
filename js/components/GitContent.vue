<template>
  <k-inside>
    <k-view class="k-git-content-view">
      <k-header>Git Content {{ size }}</k-header>

      <section v-if="files.length" class="k-section">
          <header class="k-section-header">
            <k-headline>Uncommitted changes</k-headline>
          </header>

          <k-collection :items="statusItems" help="Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a> on how to interpret the status codes to the right." />
      </section>

      <section class="k-section">
        <header class="k-section-header">
          <k-headline>Latest {{ log.length }} changes on branch »{{ branch }}«</k-headline>

          <k-button-group
            :buttons="[
              { text: 'Pull', icon: 'download', click: pull },
              { text: 'Push', icon: 'upload', click: push },
            ]"
          />

        </header>
        <k-item v-for="commit in log" :key="commit.hash"
          :text="commit.message"
          :link="false"
          :info="formatRelative(commit.date) + ' / ' + commit.hash.substr(0, 7)"
        />
      </section>
    </k-view>
  </k-inside>
</template>
<script>
import formatDistance from 'date-fns/formatDistance'

export default {
  name: 'GitContent',
  props: {
    files: {
      type: String,
      default: []
    },
    log: {
      type: Array,
      default: []
    },
    branch: {
      type: String,
      default: ''
    },
  },
  computed: {
    statusItems () {
      const items = []

      this.files.forEach(file => {
        items.push({
          text: file.filename,
          info: file.code,
          link: false,
        })
      })

      return items
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
    formatRelative (date) {
      return formatDistance(new Date(date), new Date(), {
        addSuffix: true
      })
    }
  }
}
</script>
