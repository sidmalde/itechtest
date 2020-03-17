(function ($) {

    $.fn.refreshTweets = function (options = {}) {
        let defaultOptions = $.extend({
            'interval': 5,
            'handle': '@bbc',
            'count': 20,
            'countDownSuffix': ' until next refresh',
            'refreshAfterError': true
        }, options);

        // not to be configured through the jquery options
        const API_ENDPOINT = '/api/v1';

        let intervalManager;

        /**
         * @param {HTMLElement} element
         */
        const injectTemplate = (element) => {
            const template = `
            <div class="tweetBanner tweetNoInform"></div>
            <div class="tweetContainer"></div>
            <div class="tweetFooter"></div>
            `;
            $(element).html(template);
        };


        /**
         * This method will clear the error banner
         * @param {HTMLElement} element
         */
        const clearFooter = (element) => {
            let tweetFooter = $('.tweetFooter', element);
            tweetFooter.empty();
            tweetFooter.removeClass('tweetError');
        };

        /**
         * This method will fetch the latest tweet and display it
         * @param {HTMLElement} element
         */
        const fetchLatestTweets = (element) => {
            /*
             * Make Ajax Call To Endpoint
             */
            let handle = defaultOptions.handle;
            let count = defaultOptions.count;
            handle = encodeURI(handle);
            let queryString = {
                handle, count
            };
            const fetcher = $.get(API_ENDPOINT, queryString);

            /*
             * Let's handle our success
             */
            fetcher.done((data) => {

                // clear any existing error if we have one
                clearFooter(element);

                $(data).each((index) => {

                    const template = displayTweet(data[index]);
                    $('.tweetContainer', element).prepend(template);
                });
            });

            /*
             * Now, lets deliberate over the failure
             */
            fetcher.fail((data) => {
                let message = data.responseJSON && data.responseJSON.message ?
                    data.responseJSON.message : 'Error Has Occurred';
                message = `<strong>Error</strong>: ${message}`;
                let tweetFooter = $('.tweetFooter', element);
                tweetFooter.prepend(message);
                tweetFooter.addClass('tweetError');

                if (defaultOptions.refreshAfterError === false && intervalManager) {
                    clearInterval(intervalManager);
                }
            });
        };

        /**
         * This method will display the supplied seconds in a readable countdown format
         * @param {int} seconds
         * @returns {string}
         */
        const formatSecondsToMMSS = (seconds) => {
            let hour, minute;
            minute = Math.floor(seconds / 60);
            seconds = seconds % 60;

            return `${minute}m:${seconds}s ${defaultOptions.countDownSuffix}`;
        };

        /**
         * This method displays the current countdown to the next refresh
         * @param {HTMLElement} element
         */
        const displayCountdown = (element) => {
            let cacheCountDownInterval = defaultOptions.interval * 60;
            let countDownInterval = cacheCountDownInterval;
            intervalManager = setInterval(() => {
                countDownInterval--;
                let $tweetBanner = $('.tweetBanner', element);
                $tweetBanner.html(formatSecondsToMMSS(countDownInterval));

                if (countDownInterval <= 30) {
                    $tweetBanner.removeClass('tweetNoInform');
                    $tweetBanner.addClass('tweetInform');
                } else {
                    $tweetBanner.addClass('tweetNoInform');
                    $tweetBanner.removeClass('tweetInform');
                }

                if (countDownInterval <= 0) {
                    fetchLatestTweets(element);

                    // reset the counter
                    countDownInterval = cacheCountDownInterval;
                }
            }, 1000);
        };

        const refreshEndpoint = (element) => {
            // load first batch
            fetchLatestTweets(element);

            // set time afterwards
            displayCountdown(element);
        };

        /**
         *
         * @param {object} tweet this is the tweeter object
         * @returns {string}
         */
        const displayTweet = (tweet) => {
            const avatar = tweet['user']['profile_image_url_https'];
            const text = tweet['text'];
            const name = tweet['user']['name'];
            const screenName = '@' + tweet['user']['screen_name'];
            const retweets = tweet['retweet_count'];
            const favourites = tweet['favorite_count'];

            return `
            <div class="tweetItem">
                <img src="${avatar}" alt="" class="tweetAvatar">
                <div>
                    <div class="tweetUserName">${name}</div>
                    <div class="tweetScreenName">${screenName}</div>
                    <div>${text}</div>        
                    <div>
                        <ul class="list-inline">
                            <li>
                                <button id="tweetRetweetButton" type="button" class="btn btn-default btn-xs">
                                    &#x270d;&nbsp;Retweets (${retweets})
                                </button>
                            </li>
                            <li>
                                <button id="tweetFavButton" type="button" class="btn btn-default btn-xs">
                                    &#x2764;&nbsp;Favourites (${favourites})
                                </button>
                            </li>
                        </ul>
                    </div>        
                </div>
            </div>
            `;
        };

        /**
         *
         * @param {HTMLElement} element
         */
        const handleClickEvents = (element) => {
            $(element).on('click', '#tweetRetweetButton, #tweetFavButton', () => {
                let msg = "This doesn't do anything right now :) " +
                    "- If you really want to show off you can fix that, but don't feel you have to.";
                alert(msg);
            });
        };

        return this.each(function () {
            // inject template
            injectTemplate(this);

            refreshEndpoint(this);

            handleClickEvents(this);
        });

    }

}(window.jQuery));