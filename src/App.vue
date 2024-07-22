<template>
  <div class="">
    <h1 class="wp-heading-inline">Fabio Mezzomo Plugin</h1>
    <nav class="nav-tab-wrapper">
      <router-link class="nav-tab" to="/table">{{ __('Table', 'fm-plugin') }}</router-link>
      <router-link class="nav-tab" to="/graph">{{ __('Graph', 'fm-plugin') }}</router-link>
      <router-link class="nav-tab" to="/settings">{{ __('Settings', 'fm-plugin') }}</router-link>
    </nav>
    <router-view class="fm-content"></router-view>
  </div>
</template>

<script>
export default {
  name: 'fm-plugin-app',
};
</script>


<style scoped>
body {
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
}

.wp-heading-inline {
  padding: 30px;
}

h1.wp-heading-inline {
  font-size: 24px;
  font-weight: 600;
}

.nav-tab-wrapper {
  min-height: 58px;
  background-color: #fff;
  font-size: 14px;
  padding: 0 20px;
}

.nav-tab {
    border: none;
    background: none;
    box-shadow: none;
    color: #666;
    
    display: inline-block;
    margin-right: 30px;
    padding: 20px 0 18px 0;
    text-decoration: none;

    cursor: pointer;
}

.nav-tab.router-link-active {
  border-bottom: 2px solid #e27730;
}

.nav-tab:hover {
  border-bottom: 2px solid #999;
}

</style>