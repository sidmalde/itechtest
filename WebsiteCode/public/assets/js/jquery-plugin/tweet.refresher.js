(function ($) {

    $.fn.loadIframe = function (options = {}) {

        console.log('aaa');

        let defaultOptions = $.extend({
            'handle': '@codinghorror',
            'count': 20,
        }, options);

        const IFRAME_ENDPOINT = '/iframe';

        let handle = defaultOptions.handle;
        let count = defaultOptions.count;
        handle = encodeURI(handle);
        let queryString = {
            handle, count
        };

        const fetcher = $.get(IFRAME_ENDPOINT, queryString);

        fetcher.done((data) => {
            $("#ifTweets").html(data);
        });

        fetcher.fail((data) => {
        });
    };

    $.fn.refreshTweets = function (options = {}) {
        let defaultOptions = $.extend({
            'interval': 5,
            'handle': '@bbc',
            'count': 20,
            'countDownSuffix': ' until next refresh',
            'refreshAfterError': true,
            'getUserInfo': false,
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
            <div class="tweetHeader"></div>
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
                tweets = data['tweets'];
                userInfo = data['userInfo'];
                // clear any existing error if we have one
                clearFooter(element);

                if (defaultOptions.getUserInfo) {
                    const tweetHeaderTemplate = displayUserInfo(userInfo); 
                    $('.tweetHeader', element).html(tweetHeaderTemplate);
                    defaultOptions.getUserInfo = false;
                }

                $(tweets).each((index) => {

                    const template = displayTweet(tweets[index]);
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


        const displayUserInfo = (userInfo) => {
            const avatar = userInfo[0]['profile_image_url_https'];
            const fullName = userInfo[0]['name'];
            const handle = '@' + userInfo[0]['screen_name'];
            const location = userInfo[0]['location'];
            const description = userInfo[0]['description'];
            
            const followerCountRaw = Math.round(userInfo[0]['followers_count'] * 10) / 10;
            const followerCount = followerCountRaw.toFixed(1);

            const followingCountRaw = Math.round(userInfo[0]['friends_count'] * 10) / 10;
            const followingCount = followingCountRaw.toFixed(1);

            const websiteText = userInfo[0]['entities']['url']['urls'][0]['display_url'];
            const websiteLink = userInfo[0]['entities']['url']['urls'][0]['url'];

            return `
            <div class="row">
                <div class="col-sm-3 text-center">
                    <img src="${avatar}" />
                </div>
                <div class="col-sm-9">
                    <div class="userFullName text-center">${fullName}</div>
                    <div class="userInfo text-center"><em>@${handle}</em> | <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> ${location}</div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-sm-6">
                    <div class="userInfo"><em>${description}</em></div>
                </div>
                <div class="col-sm-6">
                    <div class="userInfo text-right"><strong>${followerCount}</strong> Followers</div>
                    <div class="userInfo text-right"><strong>${followingCount}</strong> Following</div>
                    <div class="userInfo text-right"><a href="${websiteLink}"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> ${websiteText}</a></div>
                </div>
            </div>
            `;
        }

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