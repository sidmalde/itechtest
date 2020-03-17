<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="<?php echo asset('css/libs/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/home/iframe-widget.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/home/jquery-widget.css') ?>" rel="stylesheet">
    <script src="<?php echo asset('js/libs/jquery.min.js') ?>"></script>
    <script src="<?php echo asset('js/jquery-plugin/tweet.refresher.js') ?>"></script>
    
    <style>
        .tweetContainer {
            max-height: none
        }
    </style>
</head>
<body>
<div id="tweetLoader" style="width: 100%; height: 100%">
    <a class="twitter-timeline" href="https://twitter.com/codinghorror">Tweets by codinghorror</a> 
</div>
    <script>
    (function() {
        // See https://dev.twitter.com/web/javascript/creating-widgets#create-timeline
        var dataSource = {
          sourceType: 'profile',
          screenName: 'codinghorror'
        };

        // Your HTML element's ID
        var target = document.getElementById('tweetLoader');

        // See https://dev.twitter.com/web/embedded-timelines/parameters
        var options = {
          chrome: 'nofooter',
          height: 800,
          width: 500
        };

        twttr.ready(function(twttr) {
          twttr.widgets.createTimeline(dataSource, target, options);
        });
      })()
  

</script>
</body>
</html>