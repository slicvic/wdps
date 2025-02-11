<?php require_once(__DIR__ . '/config.php') ?>
<?php require_once(__DIR__ . '/bootstrap.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-141288767-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-141288767-1');
    </script>
    <!-- End Google Analytics -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= META_DESC ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta property="og:url" content="<?= $urlHelper->baseUrl() ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $config['site']['title'] ?>">
    <meta property="og:description" content="<?= META_DESC ?>">
    <meta property="og:image" content="">
    <title><?= $config['site']['title'] ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Baloo">
    <link rel="stylesheet" href="assets/css/app.min.css?_=<?= $config['cb'] ?>">
</head>
<body>
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <!-- End Load Facebook SDK for JavaScript -->
    <div class="app container d-none js-app">
        <header>
            <h1 class="logo">
                <strong>W</strong>hat <strong>D</strong>o <strong>P</strong>eople <strong>S</strong>ay<strong>?</strong>
            </h1>
            <h2 class="subhead" v-show="!showResults">
                    Enter 2 phrases and see which one is more popular. 
                <span class="d-sm-block">
                    For example, do most people say <strong class="text-secondary" v-html="examples[0]"></strong> or <strong class="text-secondary" v-html="examples[1]"></strong>
                </span> 
            </h2>
        </header>
        <main class="row">
            <div class="col-md-6 mx-auto">
                <form class="form" action="search.php" method="post" v-show="!showResults">
                    <div  v-for="(phrase, i) in phrases">
                        <div class="form__vs" v-show="i > 0">vs.</div>
                        <div class="d-flex form__input-row">
                            <div class="flex-grow-1">
                                <input
                                    class="form__input form-control form-control-lg js-phrase-input"
                                    type="text"
                                    maxlength="100"
                                    v-bind:placeholder="placeholders[i]"
                                    v-bind:class="{ 'is-invalid': validationErrors[i] }"
                                    v-bind:disabled="searching"
                                    v-on:keyup="handlePhraseInputKeyup(i)"
                                    v-model="phrases[i]">
                                <div class="invalid-feedback" v-show="validationErrors[i]">{{ validationErrors[i] }}</div>
                            </div>
                            <div class="flex-shrink-1 align-self-center">
                                <button 
                                    type="button"
                                    class="form__remove-btn btn btn-link"
                                    v-on:click="removePhrase(i)"
                                    v-show="showRemovePhraseBtn"
                                    v-bind:disabled="searching">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-link form__add-btn"
                        v-on:click="addPhrase"
                        v-bind:disabled="searching"
                        v-show="showAddPhraseBtn">
                        Add Another Phrase
                    </button>
                    <button type="button" class="btn btn-lg form__search-btn" v-on:click="search" v-bind:disabled="searching">
                        {{ searching ? 'Searching...' : 'Search'}}
                    </button>
                </form>
                <div class="results" v-show="showResults">
                    <div class="results__header">
                        <h5 v-show="!results">Zero, zilch, zip, nada, nothing!</h5>
                        <div v-show="results" v-for="(r, i) in results">
                            <span v-bind:style="{ color: chartLegendColors[i] }">{{ r.percent }}%</span> <span class="text-secondary">say</span> <strong v-bind:style="{ color: chartLegendColors[i] }">{{ r.phrase }}</strong>
                        </div>
                    </div>
                    <div class="results__chart" v-show="results">
                        <canvas ref="chartCanvas"></canvas>
                    </div>
                    <div class="results__share" v-show="results">
                        Share
                        <form>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly ref="shareUrlInput" v-model="shareUrl">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-dark" type="button" v-on:click="copyShareUrl">Copy</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <button type="button" class="results__try-again-btn btn btn-link" v-on:click="tryAgain">Try Again!</button>
                </div>
            </div>
        </main>
        <footer class="footer">
            <div class="mb-3">
                <div class="fb-share-button" data-layout="button_count" data-href="<?= $urlHelper->baseUrl() ?>"></div>
            </div>
            <?php /*
            <small>With <i class="fa fa-heart"></i> by <a href="http://www.slicvic.com">slicvic.com</a></small>
            */ ?>
        </footer>
    </div>
    <script>
        var appConfig = {
            minPhrases: <?= $config['min_phrases'] ?>,
            maxPhrases: <?= $config['max_phrases'] ?>,
            examples: <?= json_encode($config['examples']) ?>,
            referer: '<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' ?>',
            phrases: []
        };
        try {
            <?php 
                foreach ($phrases as $p) {
                    echo "appConfig.phrases.push(\"$p\");";
                }
            ?>
        } catch(e) {}
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.js"></script>
    <script src="assets/js/app.min.js?_=<?= $cb ?>"></script>
</body>
</html>