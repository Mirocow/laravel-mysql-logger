<?php

namespace ITelmenko\Logger\Monolog\Handler;

use Exception;
use ITelmenko\Logger\Laravel\Exceptions\MysqlLoggerInsertException;
use ITelmenko\Logger\Laravel\Models\Log;
use Monolog\Handler\AbstractProcessingHandler;

class MysqlHandler extends AbstractProcessingHandler
{
    private $config;

    public function setConfig(array $config)
    {
        $this->config = $config;
        $table      = $this->config['table'] ?? 'log';
        $connection = $this->config['connection'] ?? 'mysql';
        $days = $this->config['days'] ?? 30;
        try {
            Log::on($connection)->make()
                ->setTable($table)
                ->whereDate( 'created_at', '<=', now()->subDays($days))
                ->delete();
        } catch (Exception $e) {
            throw new MysqlLoggerInsertException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array|Monolog\LogRecord $record
     * @return void
     */
    protected function write($record): void
    {
        $table      = $this->config['table'] ?? 'log';
        $connection = $this->config['connection'] ?? 'mysql';
        try {
            Log::on($connection)->make([
                'instance'    => gethostname(),
                'channel'     => $record['channel'],
                'message' => $record['message'],
                'level'   => $record['level_name'],
                'context' => $record['context']
            ])->setTable($table)->save();
        } catch (Exception $e) {
            throw new MysqlLoggerInsertException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
