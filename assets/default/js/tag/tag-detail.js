new Vue({
    el: '#tag_detail',
    data: function(){
        return {
          tabsActive_A: 'is-active',
          tabsActive_B: '',
          tabsCenter_A: '',
          tabsCenter_B: 'hidden',
        }
    },
    mounted() {
        
    },
    methods: {
        tabs_A :function (){
            this.tabsActive_A = 'is-active';
            this.tabsActive_B = '';
            this.tabsCenter_A = '';
            this.tabsCenter_B = 'hidden';
        },
        tabs_B :function (){
            this.tabsActive_A = '';
            this.tabsActive_B = 'is-active';
            this.tabsCenter_A = 'hidden';
            this.tabsCenter_B = 'show';
        },
    },
});