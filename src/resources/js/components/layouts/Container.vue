<template>
  <div class="container">
    <slot />
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.container {
  @apply w-full max-w-full flex min-h-screen h-full bg-[var(--container-background-color)];
}
</style>
