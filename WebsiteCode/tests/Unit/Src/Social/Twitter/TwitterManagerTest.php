<?php


namespace itechTest\Tests\Unit\Src\Social\Twitter;


use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerMissingCredentialException;
use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerMissingHandleException;
use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerRequestException;
use itechTest\Components\Social\Twitter\Request\TwitterHttpRequest;
use itechTest\Components\Social\Twitter\TwitterManager;
use itechTest\Tests\TestCase;

/**
 * Class TwitterManagerTest
 *
 * @package itechTest\Tests\Unit\Src\Social\Twitter
 */
class TwitterManagerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!\extension_loaded('curl')) {
            $this->markTestSkipped('The curl extension is needed to run the twitter manager');
        }
    }


    /**
     * @test
     */
    public function when_AnyOfTheSetupCredentialsIsMissing_Then_ExceptionIsThrown(): void
    {
        // Expect
        $this->expectException(TwitterManagerMissingCredentialException::class);
        $this->expectExceptionMessage('You Are Missing Or More Credentials For The TwitterManager To Work');

        // Act
        new TwitterManager('', '', '', '');

        // Assert
    }

    /**
     * @test
     */
    public function when_CountIsSetToAValueHigherThanMaxAllowed_Then_TheMaxAllowableValueIsSet(): void
    {
        // Arrange
        $newValue = 1000;
        $expected = 120;

        // Act
        /** @var TwitterManager $twitterManager */
        $twitterManager = new TwitterManager('a', 'b', 'c', 'd');
        $actual = $twitterManager->setCount($newValue)->getCount();

        // Assert
        $this->assertNotEquals($newValue, $actual);
        $this->assertEquals($expected, $actual);
        $this->assertEquals(TwitterManager::MAX_ALLOWABLE_COUNT, $expected);
    }

    /**
     * @test
     */
    public function when_CountIsSetToAValueLowerThanMinAllowed_Then_TheMinAllowableValueIsSet(): void
    {
        // Arrange
        $newValue = 0;
        $expected = 20;

        // Act
        /** @var TwitterManager $twitterManager */
        $twitterManager = new TwitterManager('a', 'b', 'c', 'd');
        $actual = $twitterManager->setCount($newValue)->getCount();

        // Assert
        $this->assertNotEquals($newValue, $actual);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @test
     */
    public function when_NoTwitterHandleHasBeenSetAndRequestIsInitiated_Then_ExceptionIsThrown(): void
    {
        // Expect
        $this->expectException(TwitterManagerMissingHandleException::class);
        $this->expectExceptionMessage('You need to set a twitter handle before executing a request');

        // Act
        $twitterManager = new TwitterManager('a', 'b', 'c', 'd');
        $twitterManager->initiateRequest();

        // Assert
    }

    /**
     * @test
     */
    public function when_ThereIsAnErrorFromTheRequestHandler_Then_HasResponseExceptionIsTrue(): TwitterManager
    {
        // Arrange
        $requestHandler = $this->getMockBuilder(TwitterHttpRequest::class)
            ->setMethods(['initiateRequest', 'getError'])
            ->getMock();
        $requestHandler->expects($this->once())->method('getError')
            ->willReturn(true);
        $requestHandler->expects($this->once())->method('initiateRequest')
            ->willReturn(false);

        // Act
        /** @var TwitterManager $twitterManager */
        $twitterManager = new TwitterManager('a', 'b', 'c', 'd');
        $twitterManager->setTwitterHandle('blah');
        $twitterManager->setRequestHandler($requestHandler);
        $twitterManager->initiateRequest();

        // Assert
        $this->assertTrue($twitterManager->hasResponseException());
        return $twitterManager;
    }

    /**
     * @test
     * @depends when_ThereIsAnErrorFromTheRequestHandler_Then_HasResponseExceptionIsTrue
     * @param TwitterManager $twitterManager
     */
    public function when_ThereIsAnErrorFromTheRequestHandler_Then_getResponseExceptionReturnsValidInstance(
        TwitterManager $twitterManager): void
    {
        // Arrange

        // Act

        // Assert
        $this->assertInstanceOf(TwitterManagerRequestException::class,$twitterManager->getResponseException());
    }

    /**
     * @test
     */
    public function when_TheRequestHandlerFails_Then_HasResponseOfRequestExceptionIsSameAsOfTheHandler():void
    {
        // Arrange
        $handlerErrorMessage = 'Sample Error Message';
        $requestHandler = $this->getMockBuilder(TwitterHttpRequest::class)
            ->setMethods(['initiateRequest', 'getError'])
            ->getMock();
        $requestHandler->expects($this->once())->method('getError')
            ->willReturn(true);
        $requestHandler->expects($this->once())->method('initiateRequest')
            ->willReturn($handlerErrorMessage);

        // Act
        /** @var TwitterManager $twitterManager */
        $twitterManager = new TwitterManager('a', 'b', 'c', 'd');
        $twitterManager->setTwitterHandle('blah');
        $twitterManager->setRequestHandler($requestHandler);
        $twitterManager->initiateRequest();

        // Assert
        $this->assertTrue($twitterManager->hasResponseException());
        $this->assertEquals($handlerErrorMessage,$twitterManager->getResponseException()->getErrorResponse());
    }

}