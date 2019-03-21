window.app = new Vue({
    el: '#app',
    data: {
        searching: false,
        showResult: false,
        q: ['' , ''],
        result: []
    },
    methods: {
        addQ: function() {
            this.showResult = false;
            this.q.push('');
        },
        removeQ: function(i) {
            this.showResult = false;
            this.q.splice(i, 1);
        },
        search: function() {
            this.showResult = false;
            this.searching = true;

            var that = this;
            var params = [];

            this.q.forEach(function(q) {
                params.push('q[]=' + q);
            });

            $.getJSON('/search.php', params.join('&')).done(function(res) {
                that.searching = false;
                that.showResult = true;

                if (typeof res != 'object') {
                    return;
                }

                that.result = res;

              /*  data.forEach(d => {
                    that.result.push(d);
                });

                setTimeout(function() {
                    var canvas = document.getElementById('chart');
                    var ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    new Chart(canvas, {
                        type: 'pie',
                        data: {
                            labels: that.q,
                            datasets: [{
                                backgroundColor: ['#3e95cd', '#8e5ea2'],
                                data: that.result
                            }]
                        }
                    });
                }, 100);*/
            }).fail(function() {
                that.searching = false;
                that.showResult = true;
            });
        }
    }
});