(function(Vue, $, Chart) {

    new Vue({
        el: '.js-app',
        data: {
            searching: false,
            showResults: false,
            validationErrors: [],
            maxPhrases: 3,
            minPhrases: 2,
            //phrases: ['jordan goat', 'kobe goat', 'lebron goat'],
            phrases: ['', ''],
            examples: ['Jordan GOAT', 'LeBron GOAT', 'Kobe GOAT'],
            chart: null,
            chartColors: ['#5d5d5a', '#ffcdab', '#d2c8c8'],
            results: {}
        },
        computed: {
            showAddPhraseBtn: function() {
                return this.phrases.length < this.maxPhrases;
            },
            showRemovePhraseBtn: function() {
                return this.phrases.length > this.minPhrases;
            }
        },
        mounted: function() {
            $(this.$el).removeClass('d-none');
            $(this.$el).on('keypress', '.js-phrase-input', function(e) {
                if (e.which === 13) {
                    this.search();
                }
            }.bind(this));
        },
        methods: {
            addPhrase: function() {
                if (this.phrases.length < this.maxPhrases) {
                    this.phrases.push('');
                }
            },
            removePhrase: function(index) {
                this.phrases.splice(index, 1);
            },
            hideResults: function() {
                this.showResults = false;
            },
            handlePhraseInputKeyup: function(index) {
                this.validatePhrases(index);
            },
            /**
             * Validate all phrases or the phrase at the specified index.
             * @param {integer} index Index of the phrase to validate.
             * @return {boolean}
             */
            validatePhrases: function(index) {
                var valid = true;

                for (var i = 0; i < this.phrases.length; i++) {
                    if (typeof index !== 'undefined' && index != i) {
                        continue;
                    }
                    
                    var phrase = this.phrases[i].trim().replace(/\s\s+/g, ' ');

                    if (phrase == '') {
                        valid = false;
                        Vue.set(this.validationErrors, i, 'Phrase cannot be blank!');
                    } else if (phrase.split(' ').length < 2) {
                        valid = false;
                        Vue.set(this.validationErrors, i, 'Phrase must be 2 or more words!');
                    } else {
                        Vue.set(this.validationErrors, i, '');
                    }
                }

                return valid;
            },
            search: function() {
                if (!this.validatePhrases()) {
                    return;
                }

                this.showResults = false;
                this.searching = true;
                var that = this;
                var params = [];
                this.phrases.forEach(function(phrase) {
                    params.push('phrases[]=' + phrase);
                });

                $.getJSON('/api/search.php', params.join('&')).done(function(response) {
                    if (typeof response.results !== 'object') {
                        that.results = false;
                        that.searching = false;
                        that.showResults = true;
                        return;
                    }

                    that.results = response.results;

                    // Prepare chart data
                    var chartData = [];
                    var chartLabels = [];
                    that.results.forEach(function(r) {
                        chartLabels.push(r.phrase);
                        chartData.push(r.percent);
                    });

                    // Clear canvas
                    var canvas = $(that.$refs.chart_canvas)[0];
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        
                    // Destroy old chart
                    if (that.chart) {
                        that.chart.destroy();
                    }

                    that.searching = false;
                    that.showResults = true;

                    // Render chart (small timeout needed for rotate animation)
                    setTimeout(function() {
                        that.chart = new Chart(canvas, {
                            type: 'pie',
                            options: {
                                legend: {
                                    display: false
                                }
                            },
                            data: {
                                labels: chartLabels,
                                datasets: [{
                                    data: chartData,
                                    backgroundColor: that.chartColors
                                }]
                            }
                        });
                    }, 100);
                }).fail(function() {
                    that.results = false;
                    that.showResults = true;
                    that.searching = false;
                });
            }
        }
    });
    
})(window.Vue, window.jQuery, window.Chart);