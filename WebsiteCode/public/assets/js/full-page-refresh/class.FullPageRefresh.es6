class FullPageRefresh {
    _countDownHolderId;

    /**
     *
     * @param {int} interval This is the refresh interval in minutes
     * @param {string} countDownHolderId
     */
    constructor(interval = 3, countDownHolderId) {
        this._interval = interval;
        this._countDownHolderId = countDownHolderId;
    }

    /**
     *
     * @returns {int}
     */
    getInterval() {
        return this._interval;
    }


    /**
     * This method will return the id of the dom element to be used to hold the countdown
     * @returns {string}
     */
    getCountDownHolderId() {
        return this._countDownHolderId;
    }


    initiateCountDown() {
        /*
         * Check if there is a document to even hold the countdown, if not sleep silently
         */
        if (document.getElementById(this.getCountDownHolderId()) === null) {
            return;
        }

        let countDownInterval = 60 * this.getInterval();
        let refreshTimer = setInterval(() => {
            countDownInterval--;
            document.getElementById(this.getCountDownHolderId()).textContent = FullPageRefresh.formatSecondsToMMSS(countDownInterval);
            if (countDownInterval <= 0) {
                // clear the interval
                clearInterval(refreshTimer);
                // reload the page
                document.location.reload();
            }
        }, 1000);
    }

    /**
     * This method will display the supplied seconds in a readable countdown format
     * @param seconds
     * @returns {string}
     */
    static formatSecondsToMMSS(seconds) {
        let hour, minute;
        minute = Math.floor(seconds / 60);
        seconds = seconds % 60;

        return minute + ':' + seconds;
    }
}