<template>
  <k-inside>
    <k-view class="k-git-content-view">
      <k-header>Git Content {{ size }}</k-header>

      <section class="k-section">
        <header class="k-section-header">
          <k-headline>Latest {{ log.length }} changes on branch »{{ branch }}«</k-headline>

          <k-button-group
            :buttons="[
              { text: 'Pull', icon: 'download', click: pull },
              { text: 'Push', icon: 'upload', click: push }
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
    log: {
      type: Array,
      default: []
    },
    branch: {
      type: String,
      default: ''
    },
  },
  methods: {
    pull: async function () {
      await panel.app.$api.post('/git-content/pull')
    },
    push: async function () {
      await panel.app.$api.post('/git-content/push')
    },
    formatRelative (date) {
      return formatDistance(new Date(date), new Date(), {
        addSuffix: true
      })
    }
  }
}
</script>
