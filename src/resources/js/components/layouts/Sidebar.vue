<template>
  <div class="sidebar">
    <slot />
  </div>
</template>

<style scoped>
.sidebar {
  @apply w-full max-w-[497px];
  max-height: calc(100vh - 80px);
  min-height: calc(100vh - 80px);
}
</style>