<div class="jumbotron">
    <h1>iTech Technical Test</h1>
</div>

<div class="row">
    <div class="col-xs-10 col-lg-offset-1">
        <div class="page-header">
            <p>
                This is a custom framework written by one our team to show some of the work we do and for you to use as
                part of this test.
                It connects to the Twitter API and retrieves a user's most recent tweets.
            </p>
            <p>
                We would like you to do the following:
            </p>
            <ol>
                <li><strong>Task</strong><br />
                    Edit <code>tweet.refresher.js</code> to include an element above the list of
                    <a href="http://twitter.com/wsop" target="_blank">@wsop</a> tweets below containing information
                    about the wsop user (eg. location, bio, etc).<br />
                    <strong>Why?</strong><br />
                    We want to see your problem solving skills, and it's a chance for you to show us a nice layout.
                </li>
                <li><strong>Task</strong><br />
                    Use vanilla JavaScript to embed the tweets from
                    <a href="http://twitter.com/codinghorror" target="_blank">@codinghorror</a> into the iframe
                    below that has the id 'ifTweets'. This <strong>does not</strong> need the user information header.<br />
                    <strong>Why?</strong><br />
                    Our newer sites are built with Vue.js, but being able to write plain JavaScript is always useful.
                    Bonus points for using ES6!
                </li>
                <li><strong>Task</strong><br />
                    Add a database layer to cache the tweets so we can display them as fast as we can.
                    You can access the DB in your Docker container by running the command <code>mysql</code>.<br />
                    <strong>Why?</strong><br />
                    A lot of our database work is done with MySQL, so we'd like to see how comfortable you are with it.
                    Also, this is a trickier problem than it might appear (for example - how do you add new tweets to
                    the cache?).
                </li>
                <li>If you find any bugs in the code we provide, feel free to fix them for bonus points!
                    Don't forget to note down what it was you found.</li>
            </ol>
            <div class="alert alert-info">
                <p><strong>Note: </strong> Feel free to add libraries (eg. Doctrine) or tools (eg. PHPMyAdmin) to this
                project if you wish.</p>
            </div>
        </div>
        <hr>

        <div class="row" style="margin-bottom: 40px">
            <div class="col-sm-6">
                <div id="jqTweets"></div>
            </div>
            <div class="col-sm-6">
                <iframe id="ifTweets"></iframe>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#jqTweets").refreshTweets({
            handle: "wsop"
        });
    });
</script>