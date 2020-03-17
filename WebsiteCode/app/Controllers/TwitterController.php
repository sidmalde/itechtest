<?php

namespace itechTest\App\Controllers;

use itechTest\Components\Social\Twitter\TwitterManager;


/**
 * Class TwitterController
 *
 * @package itechTest\App\Controllers
 */
class TwitterController extends BaseController
{
    /**
     * This will reload the timeline by refreshing
     *
     * @throws \Exception
     */
    public function getTwitter(): void
    {
        $minimumRefreshTime = 1;
        $refreshMinutes = (int)array_get_item($_GET, 'refresh', $minimumRefreshTime);
        $refreshMinutes = max($minimumRefreshTime, $refreshMinutes);
        $listOfCounts = range(20, TwitterManager::MAX_ALLOWABLE_COUNT, 20);
        $listOfRefreshMins = range($minimumRefreshTime, 30, 5);


        // fetch the tweets
        $tweets = $this->fetchTweets();
        [
            'twitterHandle'    => $twitterHandle,
            'count'            => $count,
            'response'         => $response,
            'errorMessage'     => $errorMessage,
            'errorHasOccurred' => $errorHasOccurred,
            //'statusCode'       => $statusCode,
        ] = $tweets;


        // decode the json response in the items
        $items = json_decode($response, true);

        $data = compact('items', 'twitterHandle',
            'listOfCounts', 'refreshMinutes', 'errorHasOccurred',
            'errorMessage', 'listOfRefreshMins', 'count');
        \view()
            ->addHeadCss(asset('/css/home/iframe-widget.css'))
            ->render('home.twitter.timeline', $data);
    }


    /**
     * @return array
     */
    private function fetchTweets(): array
    {
        /** @var TwitterManager $twitterManager */
        $twitterManager = $this->getApplication()['twitter'];
        $count = (int)array_get_item($_GET, 'count');
        $twitterHandle = array_get_item($_GET, 'handle');

        // keep bbc as a fallback
        $twitterHandle = empty($twitterHandle) ? 'bbc' : $twitterHandle;


        $errorHasOccurred = false;
        $errorMessage = '';
        $response = '';
        $returnData = [];
        if (null !== $twitterHandle) {
            // set the details for fetching the data here
            $twitterManager = $twitterManager->setUrl('userTimeline')->setTwitterHandle($twitterHandle)->setCount($count);
            $returnData['tweets'] = json_decode($twitterManager->initiateRequest());

            // var_dump($response);
            // die;

            $twitterManager = $this->getApplication()['twitter'];
            $twitterManager = $twitterManager->setUrl('userInfo')->setTwitterHandle($twitterHandle)->setCount(1);
            $returnData['userInfo'] = json_decode($twitterManager->initiateRequest());

            // var_dump($response2);
            // die;

            $response = json_encode($returnData);

            /*
             * Error Handling Mechanism here
             */
            if ($twitterManager->hasResponseException()) {
                $errorHasOccurred = true;
                $responseException = $twitterManager->getResponseException();
                $errorMessage = $responseException ? $responseException->getMessage() : 'Error Has Occurred';
            }
        }

        $count = $twitterManager->getCount(); // refresh the count
        $statusCode = $twitterManager->getResponseStatusCode();

        return compact('count', 'twitterHandle', 'response', 'errorMessage', 'errorHasOccurred', 'statusCode');

    }

    /**
     * @throws \Exception
     */
    public function getApiTweets(): void
    {
        // fetch the tweets
        $tweets = $this->fetchTweets();
        // var_dump($tweets);
        // die;
        [
            'response'         => $response,
            'errorMessage'     => $errorMessage,
            'errorHasOccurred' => $errorHasOccurred,
            'statusCode'       => $statusCode,
        ] = $tweets;

        /*
         * Error Handling Mechanism For API
         */
        if ($errorHasOccurred) {
            $error = [
                'message'     => $errorMessage,
                'status_code' => $statusCode,
            ];

            $response = json_encode($error);
        }

        header('Content-Type: application/json');
        http_response_code($statusCode);

        echo $response;
    }

    /**
     * @throws \Exception
     */
    public function getIframe(): void
    {
        $count = (int)array_get_item($_GET, 'count');
        $twitterHandle = array_get_item($_GET, 'handle', 'bbc');
        $data = compact('count', 'twitterHandle');

        \view()->setThemeLayout('blank')->render('iframe.index', $data);
    }

}