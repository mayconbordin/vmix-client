<?php

use Mayconbordin\Vmix\Vmix;

class VmixTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Vmix
     */
    protected $vmix;

    protected function setUp()
    {
        $this->vmix = new Vmix('http://192.168.103.128:8088');
    }

    public function testInfo()
    {
        $info = $this->vmix->info();
        print_r($info);
    }

    public function testInputs()
    {
        $inputs = $this->vmix->inputs();
        print_r($inputs);
    }

    public function testSetText()
    {
        $inputs = $this->vmix->inputs();

        $this->assertGreaterThanOrEqual(1, sizeof($inputs));

        $input = $inputs[0];

        $this->assertGreaterThanOrEqual(2, sizeof($input['text']));

        $text = $input['text'][0];

        $this->vmix->setText($input['key'], $text['name'], 'Test');
    }

    public function testOptions()
    {
        $options = [
            'Function'     => 'SetText',
            'Input'        => 'ab09d985-bec6-4d62-9a66-455d3b416bba',
            'SelectedName' => 'Message',
            'Value'        => 'Test'
        ];

        $url = $this->vmix->buildOptions($options);

        var_dump($url);
    }
}