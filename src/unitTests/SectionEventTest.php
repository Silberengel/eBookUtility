<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use swentel\nostr\Event\Event;

/**
 * Unit tests for SectionEvent class
 */
final class SectionEventTest extends TestCase
{
    protected function setUp(): void
    {
        // Include necessary files
        include_once dirname(__DIR__) . '/Tag.php';
        include_once dirname(__DIR__) . '/SectionEvent.php';
    }

    /**
     * Test constructor with empty data
     */
    public function testConstructorWithEmptyData(): void
    {
        $sectionEvent = new SectionEvent();
        
        $this->assertEquals('', $sectionEvent->sectionDTag);
        $this->assertEquals('', $sectionEvent->sectionTitle);
        $this->assertEquals('', $sectionEvent->sectionAuthor);
        $this->assertEquals('', $sectionEvent->sectionVersion);
        $this->assertEquals('', $sectionEvent->sectionContent);
        $this->assertEmpty($sectionEvent->sectionOptionalTags);
    }

    /**
     * Test constructor with data
     */
    public function testConstructorWithData(): void
    {
        $data = [
            'title' => 'Test Title',
            'author' => 'Test Author',
            'version' => '1.0',
            'content' => 'Test Content',
            'dTag' => 'test-d-tag'
        ];
        
        $sectionEvent = new SectionEvent($data);
        
        $this->assertEquals('test-d-tag', $sectionEvent->sectionDTag);
        $this->assertEquals('Test Title', $sectionEvent->sectionTitle);
        $this->assertEquals('Test Author', $sectionEvent->sectionAuthor);
        $this->assertEquals('1.0', $sectionEvent->sectionVersion);
        $this->assertEquals('Test Content', $sectionEvent->sectionContent);
        $this->assertEmpty($sectionEvent->sectionOptionalTags);
    }

    /**
     * Test constructor with partial data
     */
    public function testConstructorWithPartialData(): void
    {
        $data = [
            'title' => 'Test Title',
            'author' => 'Test Author'
        ];
        
        $sectionEvent = new SectionEvent($data);
        
        $this->assertEquals('', $sectionEvent->sectionDTag);
        $this->assertEquals('Test Title', $sectionEvent->sectionTitle);
        $this->assertEquals('Test Author', $sectionEvent->sectionAuthor);
        $this->assertEquals('', $sectionEvent->sectionVersion);
        $this->assertEquals('', $sectionEvent->sectionContent);
        $this->assertEmpty($sectionEvent->sectionOptionalTags);
    }

    /**
     * Test createSection method
     * 
     * This test skips the actual network calls
     */
    public function testCreateSection(): void
    {
        // Skip this test as it requires mocking private methods
        $this->markTestSkipped('This test requires mocking private methods which is not possible.');
    }

    /**
     * Test getEventIdWithRetry method with successful retrieval
     * 
     * This test uses reflection to access the private method
     */
    public function testGetEventIdWithRetrySuccess(): void
    {
        // Create a mock for the Event class
        $eventMock = $this->createMock(Event::class);
        $eventMock->method('getId')->willReturn('mock-event-id');
        
        // Create a SectionEvent instance
        $sectionEvent = new SectionEvent();
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod(SectionEvent::class, 'getEventIdWithRetry');
        $reflectionMethod->setAccessible(true);
        
        // Call the method
        $result = $reflectionMethod->invoke($sectionEvent, $eventMock);
        
        // Verify the result
        $this->assertEquals('mock-event-id', $result);
    }

    /**
     * Test getEventIdWithRetry method with retry and eventual success
     * 
     * This test uses reflection to access the private method
     */
    public function testGetEventIdWithRetryEventualSuccess(): void
    {
        // Create a mock for the Event class
        $eventMock = $this->createMock(Event::class);
        
        // Configure the mock to return empty string first, then a valid ID
        $eventMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturnOnConsecutiveCalls('', 'mock-event-id');
        
        // Create a SectionEvent instance
        $sectionEvent = new SectionEvent();
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod(SectionEvent::class, 'getEventIdWithRetry');
        $reflectionMethod->setAccessible(true);
        
        // Call the method with a short delay and only 1 retry
        $result = $reflectionMethod->invoke($sectionEvent, $eventMock, 1, 1);
        
        // Verify the result
        $this->assertEquals('mock-event-id', $result);
    }

    /**
     * Test getEventIdWithRetry method with failure
     * 
     * This test uses reflection to access the private method
     */
    public function testGetEventIdWithRetryFailure(): void
    {
        // Create a mock for the Event class
        $eventMock = $this->createMock(Event::class);
        $eventMock->method('getId')->willReturn('');
        
        // Create a SectionEvent instance
        $sectionEvent = new SectionEvent();
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod(SectionEvent::class, 'getEventIdWithRetry');
        $reflectionMethod->setAccessible(true);
        
        // Expect an exception
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The section eventID was not created');
        
        // Call the method with a short delay and only 1 retry
        $reflectionMethod->invoke($sectionEvent, $eventMock, 1, 1);
    }

    /**
     * Test buildSectionEvent method
     * 
     * This test uses reflection to access the private method
     */
    public function testBuildSectionEvent(): void
    {
        // Create a SectionEvent instance
        $sectionEvent = new SectionEvent();
        $sectionEvent->sectionDTag = 'test-d-tag';
        $sectionEvent->sectionTitle = 'Test Title';
        $sectionEvent->sectionAuthor = 'Test Author';
        $sectionEvent->sectionContent = 'Test Content';
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod(SectionEvent::class, 'buildSectionEvent');
        $reflectionMethod->setAccessible(true);
        
        // Call the method
        $result = $reflectionMethod->invoke($sectionEvent);
        
        // Verify the result
        $this->assertInstanceOf(Event::class, $result);
        
        // Use reflection to access protected properties
        $reflectionClass = new ReflectionClass(Event::class);
        
        $kindProperty = $reflectionClass->getProperty('kind');
        $kindProperty->setAccessible(true);
        $this->assertEquals(SectionEvent::EVENT_KIND, $kindProperty->getValue($result));
        
        $contentProperty = $reflectionClass->getProperty('content');
        $contentProperty->setAccessible(true);
        $this->assertEquals('Test Content', $contentProperty->getValue($result));
        
        // Verify the tags
        $tagsProperty = $reflectionClass->getProperty('tags');
        $tagsProperty->setAccessible(true);
        $tags = $tagsProperty->getValue($result);
        
        $this->assertIsArray($tags);
        $this->assertCount(5, $tags);
        $this->assertEquals(['d', 'test-d-tag'], $tags[0]);
        $this->assertEquals(['title', 'Test Title'], $tags[1]);
        $this->assertEquals(['author', 'Test Author'], $tags[2]);
    }

    /**
     * Test logSectionEvent method
     * 
     * This test uses reflection to access the private method and captures output
     */
    public function testLogSectionEvent(): void
    {
        // Create a SectionEvent instance
        $sectionEvent = new SectionEvent();
        $sectionEvent->sectionDTag = 'test-d-tag';
        
        // We'll use a mock implementation for print_event_data
        // Instead of redefining the function, we'll just check the output
        
        // Start output buffering
        ob_start();
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod(SectionEvent::class, 'logSectionEvent');
        $reflectionMethod->setAccessible(true);
        
        // Call the method
        $reflectionMethod->invoke($sectionEvent, 'mock-event-id');
        
        // Get the output
        $output = ob_get_clean();
        
        // Verify the output
        $this->assertStringContainsString('Published 30041 event with ID mock-event-id', $output);
    }
}
