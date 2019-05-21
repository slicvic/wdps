new Vue({
    el: '#app',
    data: {
        searching: false,
        showResults: false,
        showErrors: false,
        maxPhrases: 5,
        minPhrases: 2,
        phrases: ['', ''],
        results: {
            total: 0,
            totalFormatted: 0,
            phrases: [
                // e.g.
                // {
                //     text: 'hello',
                //     total: 1000,
                //     totalFormatted: '1,000',
                //     percent: 50
                // }
            ]
        }
    },
    mounted: function() {
        $(this.$el).removeClass('d-none');
    },
    methods: {
        addPhrase: function() {
            if (this.phrases.length < this.maxPhrases) {
                this.phrases.push('');
            }
        },
        removePhrase: function(i) {
            this.phrases.splice(i, 1);
        },
        validateForm: function() {
            return !this.phrases.some(function(phrase) {
                return phrase.trim().length === 0;
            });
        },
        search: function() {
            this.showErrors = false;

            if (!this.validateForm()) {
                this.showErrors = true;
                return;
            }

            this.showResults = false;
            this.searching = true;

            var that = this;
            var params = [];

            this.phrases.forEach(function(phrase) {
                params.push('phrases[]=' + phrase);
            });

            var canvas = $(this.$refs.chart_canvas)[0];
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            $.getJSON('/search.php', params.join('&')).done(function(results) {
                that.results = results;
                that.searching = false;

                if (results.total < 1) {
                    return;
                }

                that.showResults = true;

                setTimeout(function() {
                    var chartData = [];
                    var chartLabels = [];
                    that.results.phrases.forEach(function(phrase) {
                        chartLabels.push(phrase.text);
                        chartData.push(phrase.percent);
                    });

                    new Chart(canvas, {
                        type: 'pie',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                backgroundColor: ['#3e95cd', '#8e5ea2'],
                                data: chartData
                            }]
                        }
                    });
                }, 100);
                
            }).fail(function() {
                that.searching = false;
            });
        }
    }
});