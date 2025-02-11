(function(config, Vue, $, Chart) {

    new Vue({
        el: '.js-app',
        data: {
            searching: false,
            showResults: false,
            validationErrors: [],
            shareUrl: '',
            minPhrases: config.minPhrases,
            maxPhrases: config.maxPhrases,
            examples: config.examples,
            placeholders: ['Enter a phrase', 'Enter another phrase'],
            phrases: new Array(config.minPhrases).fill(''),
            chartLegendColors: ['#ff6f6c', '#999ba7'],
            chartColors: ['#ff6f6c', '#ebebee'],
            chart: null,
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
        created: function() {
            for (var i in config.phrases) {
                this.phrases[i] = config.phrases[i];
            }
        },
        mounted: function() {
            $(this.$el).removeClass('d-none');

            $(this.$refs.shareUrlInput).focus(function() {
                $(this).select();
            });

            $(this.$el).on('keypress', '.js-phrase-input', function(e) {
                if (e.which === 13) {
                    this.search();
                }
            }.bind(this));

            if (config.phrases.length) {
                this.search();
            }
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
            tryAgain: function() {
                this.showResults = false;
            },
            copyShareUrl: function() {
                try {
                    var input = this.$refs.shareUrlInput;
                    //if (navigator.userAgent.match(/ipad|iphone/i)) {
                        var range = document.createRange();
                        range.selectNode(input);
                        var selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                    //} else {
                    //    input.select();
                    //}
                    document.execCommand('copy');
                } catch (e) {}
            },
            handlePhraseInputKeyup: function(i) {
                this.validatePhraseInput(i);
            },
            validatePhrase: function(phrase) {
                if (!(typeof phrase === 'string' && phrase.length)) {
                    return 'Phrase has to be 2 or more words';
                }
                
                if (phrase.trim().split(' ').length < 2) {
                    return 'Phrase has to be 2 or more words';
                } 
                
                return '';
            },
            validatePhraseInput: function(i) {
                var error = this.validatePhrase(this.phrases[i]);
                Vue.set(this.validationErrors, i, error);
                return !error;
            },
            validateForm: function() {
                var error = false;
                for (var i in this.phrases) {
                    if (!this.validatePhraseInput(i)) {
                        error = true;
                    }
                }
                return !error;
            },
            search: function() {
                if (!this.validateForm()) {
                    return;
                }
                
                this.showResults = false;
                this.searching = true;

                var params = [];
                this.phrases.forEach(function(phrase) {
                    params.push('q[]=' + phrase);
                });

                if (config.referer) {
                    params.push('referer=' + config.referer);
                    config.referer = '';
                }

                var that = this;

                $.getJSON('/api/search.php', params.join('&')).always(function(response) {
                    if (!(typeof response === 'object' && typeof response.results === 'object')) {
                        that.results = false;
                        that.searching = false;
                        that.showResults = true;
                        return;
                    }

                    that.results = response.results;
                    that.shareUrl = response.share_url;

                    // Prepare chart data
                    var chartData = [];
                    var chartLabels = [];
                    that.results.forEach(function(r) {
                        chartLabels.push(r.phrase);
                        chartData.push(r.percent);
                    });

                    // Init chart
                    var canvas = $(that.$refs.chartCanvas)[0];
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    if (that.chart) {
                        that.chart.destroy();
                    }

                    that.searching = false;
                    that.showResults = true;

                    window.scrollTo({
                        top: 0,
                        left: 0,
                        behavior: 'smooth'
                    });

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
                });
            }
        }
    });
    
})(window.appConfig, window.Vue, window.jQuery, window.Chart);