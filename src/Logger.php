<?php
namespace Terrazza\Dev\Logger;

use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordFlatConverter;
use Terrazza\Component\Logger\Converter\NonScalar\NonScalarJsonConverter;
use Terrazza\Component\Logger\Formatter\LogRecordFormatter;
use Terrazza\Component\Logger\Handler\ChannelHandler;
use Terrazza\Component\Logger\Handler\LogHandler;
use Terrazza\Component\Logger\LoggerInterface;
use Terrazza\Component\Logger\Logger as rLogger;
use Terrazza\Component\Logger\Utility\RecordValueConverter\LogRecordValueDateConverter;
use Terrazza\Component\Logger\Utility\RecordValueConverter\LogRecordValueExceptionConverter;
use Terrazza\Component\Logger\Writer\LogStreamFileWriter;

class Logger {
    private string $name;
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function createLogger($stream=null) : LoggerInterface {
        $logger                                     = new rLogger($this->name);
        $format                                     = [
            "message" => "{Date} {Namespace}:{Method} (#{Line}) {Message} {Context}"
        ];
        if ($stream === true) {
            $stream                                 = "php://stdout";
        }
        if (is_string($stream)) {
            $formatter                              = new LogRecordFormatter(new NonScalarJsonConverter(), $format);
            $formatter->pushConverter("Date", new LogRecordValueDateConverter());
            $formatter->pushConverter("Content.exception", new LogRecordValueExceptionConverter());
            $channel                                = new ChannelHandler($this->name-".channel",
                new LogStreamFileWriter(new FormattedRecordFlatConverter(" "), $stream, FILE_APPEND),
                $formatter
            );
            $channel->pushLogHandler(new LogHandler(rLogger::DEBUG));
            return $logger->registerChannelHandler($channel);
        } elseif ($stream === false) {
            return $logger;
        } else {
            return $logger;
        }
    }
}