<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Supersim\V1;

use Twilio\Exceptions\TwilioException;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Stream;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
class CommandList extends ListResource {
    /**
     * Construct the CommandList
     *
     * @param Version $version Version that contains the resource
     */
    public function __construct(Version $version) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [];

        $this->uri = '/Commands';
    }

    /**
     * Create a new CommandInstance
     *
     * @param string $sim The sid or unique_name of the SIM to send the Command to
     * @param string $command The message body of the command
     * @param array|Options $options Optional Arguments
     * @return CommandInstance Newly created CommandInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function create(string $sim, string $command, array $options = []): CommandInstance {
        $options = new Values($options);

        $data = Values::of([
            'Sim' => $sim,
            'Command' => $command,
            'CallbackMethod' => $options['callbackMethod'],
            'CallbackUrl' => $options['callbackUrl'],
        ]);

        $payload = $this->version->create(
            'POST',
            $this->uri,
            [],
            $data
        );

        return new CommandInstance($this->version, $payload);
    }

    /**
     * Streams CommandInstance records from the API as a generator stream.
     * This operation lazily loads records as efficiently as possible until the
     * limit
     * is reached.
     * The results are returned as a generator, so this operation is memory
     * efficient.
     *
     * @param array|Options $options Optional Arguments
     * @param int $limit Upper limit for the number of records to return. stream()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, stream()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return Stream stream of results
     */
    public function stream(array $options = [], int $limit = null, $pageSize = null): Stream {
        $limits = $this->version->readLimits($limit, $pageSize);

        $page = $this->page($options, $limits['pageSize']);

        return $this->version->stream($page, $limits['limit'], $limits['pageLimit']);
    }

    /**
     * Reads CommandInstance records from the API as a list.
     * Unlike stream(), this operation is eager and will load `limit` records into
     * memory before returning.
     *
     * @param array|Options $options Optional Arguments
     * @param int $limit Upper limit for the number of records to return. read()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, read()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return CommandInstance[] Array of results
     */
    public function read(array $options = [], int $limit = null, $pageSize = null): array {
        return \iterator_to_array($this->stream($options, $limit, $pageSize), false);
    }

    /**
     * Retrieve a single page of CommandInstance records from the API.
     * Request is executed immediately
     *
     * @param array|Options $options Optional Arguments
     * @param mixed $pageSize Number of records to return, defaults to 50
     * @param string $pageToken PageToken provided by the API
     * @param mixed $pageNumber Page Number, this value is simply for client state
     * @return CommandPage Page of CommandInstance
     */
    public function page(array $options = [], $pageSize = Values::NONE, string $pageToken = Values::NONE, $pageNumber = Values::NONE): CommandPage {
        $options = new Values($options);
        $params = Values::of([
            'Sim' => $options['sim'],
            'Status' => $options['status'],
            'Direction' => $options['direction'],
            'PageToken' => $pageToken,
            'Page' => $pageNumber,
            'PageSize' => $pageSize,
        ]);

        $response = $this->version->page(
            'GET',
            $this->uri,
            $params
        );

        return new CommandPage($this->version, $response, $this->solution);
    }

    /**
     * Retrieve a specific page of CommandInstance records from the API.
     * Request is executed immediately
     *
     * @param string $targetUrl API-generated URL for the requested results page
     * @return CommandPage Page of CommandInstance
     */
    public function getPage(string $targetUrl): CommandPage {
        $response = $this->version->getDomain()->getClient()->request(
            'GET',
            $targetUrl
        );

        return new CommandPage($this->version, $response, $this->solution);
    }

    /**
     * Constructs a CommandContext
     *
     * @param string $sid The SID that identifies the resource to fetch
     */
    public function getContext(string $sid): CommandContext {
        return new CommandContext($this->version, $sid);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Supersim.V1.CommandList]';
    }
}