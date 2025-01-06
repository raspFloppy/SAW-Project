<script>
export default {
    name: 'Navbar',
};
</script>

<template>
    <div class="navbar bg-base-100 shadow-lg">
        <div class="flex-1">
            <slot name="left"></slot>
        </div>
        <div class="flex-none gap-2">
            <slot name="right"></slot>
        </div>
    </div>
</template>