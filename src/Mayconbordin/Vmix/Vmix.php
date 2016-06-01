<?php namespace Mayconbordin\Vmix;

use GuzzleHttp\Client;

/**
 * Class Vmix
 *
 * @link http://www.vmix.com/help17/index.htm?DeveloperAPI.html
 * @package Mayconbordin\Vmix
 * @author Maycon Bordin <mayconbordin@gmail.com>
 */
class Vmix
{
    const FN_SETTEXT = "SetText";

    /**
     * The URL to the Vmix server.
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Vmix constructor.
     *
     * @param string $server The url of the Vmix server.
     */
    public function __construct($server)
    {
        $this->server = $server;

        $this->client = new Client([
            'base_uri' => $this->server
        ]);
    }

    /**
     * Get vmix server information.
     *
     * @return \SimpleXMLElement
     * @throws VmixException
     */
    public function info()
    {
        $res = $this->client->request('GET', '/api');

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error getting Vmix information: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }

        $xml = simplexml_load_string($res->getBody()->getContents());
        return $xml;
    }

    /**
     * Set text to input text field.
     *
     * @param $inputId GUID of the input to be set
     * @param $textField Text name or index of the text field
     * @param $value The value to be set
     * @throws VmixException
     */
    public function setText($inputId, $textField, $value)
    {
        $options = [
            'Function' => 'SetText',
            'Input'    => $inputId,
            'Value'    => $value
        ];

        if (is_numeric($textField)) {
            $options['SelectedIndex'] = $textField;
        } else {
            $options['SelectedName'] = $textField;
        }

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error setting text: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Start the countdown.
     *
     * @param $inputId GUID of the input to be set
     * @throws VmixException
     */
    public function startCountdown($inputId)
    {
        $options = [
            'Function' => 'StartCountdown',
            'Input'    => $inputId
        ];

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error starting the countdown: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Stop the countdown.
     *
     * @param $inputId GUID of the input to be set
     * @throws VmixException
     */
    public function stopCountdown($inputId)
    {
        $options = [
            'Function' => 'StopCountdown',
            'Input'    => $inputId
        ];

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error stopping the countdown: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Pause the countdown.
     *
     * @param $inputId GUID of the input to be set
     * @throws VmixException
     */
    public function pauseCountdown($inputId)
    {
        $options = [
            'Function' => 'PauseCountdown',
            'Input'    => $inputId
        ];

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error pausing the countdown: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Set the countdown duration.
     *
     * @param $inputId GUID of the input to be set
     * @param $value Duration as hh:mm:ss (00:00:00)
     * @throws VmixException
     */
    public function setCountdown($inputId, $value)
    {
        $options = [
            'Function' => 'SetCountdown',
            'Input'    => $inputId,
            'Value'    => $value
        ];

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error setting the countdown: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Adjust the countdown duration.
     *
     * @param $inputId GUID of the input to be set
     * @param $value Duration in seconds to adjust the countdown (negative to subtract or positive to add)
     * @throws VmixException
     */
    public function ajustCountdown($inputId, $value)
    {
        $options = [
            'Function' => 'AdjustCountdown',
            'Input'    => $inputId,
            'Value'    => $value
        ];

        $res = $this->client->request('GET', '/api/?' . $this->buildOptions($options));

        if ($res->getStatusCode() != 200) {
            throw new VmixException('Error adjusting the countdown: ' . $res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * Get list of inputs and its text fields.
     *
     * @return array
     * @throws VmixException
     */
    public function inputs()
    {
        $info = $this->info();
        $inputs = [];

        foreach ($info->inputs->input as $_input) {
            $in_vars = get_object_vars($_input);
            $input = $in_vars['@attributes'];

            $input['text'] = [];

            foreach ($_input->text as $_text) {
                $txt_vars = get_object_vars($_text);

                $input['text'][] = $txt_vars['@attributes'];
            }

            $inputs[] = $input;
        }

        return $inputs;
    }

    public function buildOptions(array $options)
    {
        $pairs = [];

        foreach ($options as $key => $value) {
            $pairs[] = $key . '=' . $value;
        }

        return implode('&', $pairs);
    }
}