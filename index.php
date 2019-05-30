<?php
    $cb = 2;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>What do people say? - Find out what phrases people say the most</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Baloo|Bungee+Shade&display=swap">
    <link rel="stylesheet" href="assets/css/app.min.css?_=<?= $cb ?>">
</head>
<body>
    <div class="container text-center d-none mt-3 js-app">
        <header>
            <h1 class="logo m-0">
                <strong>W</strong>hat <strong>D</strong>o <strong>P</strong>eople <strong>S</strong>ay<strong>?</strong>
            </h1>
            <div class="mt-2 mb-4" v-show="!showResults">
                <p class="desc">Type in some phrases to see what people say the most.</p>
                <p class="desc">For example, <strong class="text-secondary">jordan goat</strong> vs. <strong class="text-secondary">lebron goat</strong></p>
            </div>
        </header>
        <main class="row">
            <div class="col-md-6 mx-auto">
                <form class="mb-3" v-show="!showResults">
                    <div class="mb-3" v-bind:class=" { 'mb-4': i == 2 }" v-for="(phrase, i) in phrases">
                        <div class="m-2" v-show="i > 0">vs.</div>
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <input
                                    class="form-control form-control-lg phrase-input js-phrase-input"
                                    type="text"
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
                                    class="btn btn-link remove-phrase-btn"
                                    v-on:click="removePhrase(i)"
                                    v-show="showRemovePhraseBtn"
                                    v-bind:disabled="searching">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button
                            type="button"
                            class="btn btn-link add-phrase-btn"
                            v-on:click="addPhrase"
                            v-bind:disabled="searching"
                            v-show="showAddPhraseBtn">
                            Add Another Phrase
                        </button>
                    </div>
                    <button type="button" class="btn btn-lg search-btn" v-on:click="search" v-bind:disabled="searching">
                        {{ searching ? 'Searching...' : 'Search'}}
                    </button>
                </form>
                <div class="mt-2" v-show="showResults">
                    <h5 class="my-4" v-show="!results">Zero, zilch, zip, nada, nothing!</h5>
                    <div v-show="results">
                        <div v-for="(phrase, i) in results.phrases">
                            <span v-bind:style="{ color: chartColors[i] }">{{ phrase.percent }}%</span> <span class="text-secondary">say</span> <strong v-bind:style="{ color: chartColors[i] }">{{ phrase.text }}</strong>
                        </div>
                    </div>
                    <div class="chart-container mx-auto my-3" v-show="results">
                        <canvas ref="chart_canvas"></canvas>
                    </div>
                    <button type="button" class="btn btn-link try-again-btn" v-on:click="hideResults">Try Again!</button>
                </div>
            </div>
        </main>
        <footer class="text-center">
            <small>Created by <a href="http://www.slicvic.com">slicvic.com</a></small>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.js"></script>
    <script src="assets/js/app.min.js?_=<?= $cb ?>"></script>
</body>
</html>