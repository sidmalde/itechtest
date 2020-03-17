/*
 * using var deliberately instead of let because I am not certain which web browser would be used to viwew this page
 */
var startUp = function ($window) {
    var countDownSelector = 'countdownHolder';
    var fullPageRefresher = new $window.FullPageRefresh($window.REFRESH_INTERVAL, countDownSelector);

    /*
     * Start the count down right away!
     */
    fullPageRefresher.initiateCountDown();
};

startUp(window);