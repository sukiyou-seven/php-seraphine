<?php


class SnowFlakeId
{
    private int $datacenterIdBits = 5;
    private int $workerIdBits = 5;
    private int $sequenceBits = 12;

    private int $maxDatacenterId;
    private int $maxWorkerId;
    private int $sequenceMask;

    private int $timestampLeftShift;
    private int $datacenterIdShift;
    private int $workerIdShift;

    private int $datacenterId;
    private int $workerId;
    private int $sequence = 0;

    // 基准时间戳 (2022-01-01 00:00:00 UTC)
    private int $epoch = 1640995200000;

    private int $lastTimestamp = -1;

    public function __construct(int $datacenterId, int $workerId, int $sequence = 0)
    {
        $this->maxDatacenterId = -1 ^ (-1 << $this->datacenterIdBits);
        $this->maxWorkerId = -1 ^ (-1 << $this->workerIdBits);
        $this->sequenceMask = -1 ^ (-1 << $this->sequenceBits);

        $this->timestampLeftShift = $this->sequenceBits + $this->workerIdBits + $this->datacenterIdBits;
        $this->datacenterIdShift = $this->sequenceBits + $this->workerIdBits;
        $this->workerIdShift = $this->sequenceBits;

        if ($datacenterId > $this->maxDatacenterId || $datacenterId < 0) {
            throw new InvalidArgumentException(
                "Datacenter ID can't be greater than {$this->maxDatacenterId} or less than 0"
            );
        }

        if ($workerId > $this->maxWorkerId || $workerId < 0) {
            throw new InvalidArgumentException(
                "Worker ID can't be greater than {$this->maxWorkerId} or less than 0"
            );
        }

        $this->datacenterId = $datacenterId;
        $this->workerId = $workerId;
        $this->sequence = $sequence;
    }

    private function nextTimestamp(int $lastTimestamp): int
    {
        $timestamp = (int)(microtime(true) * 1000);

        while ($timestamp <= $lastTimestamp) {
            $timestamp = (int)(microtime(true) * 1000);
        }

        return $timestamp;
    }

    public function nextId(): int
    {
        $timestamp = (int)(microtime(true) * 1000);

        // 系统时钟回退检测
        if ($timestamp < $this->lastTimestamp) {
            throw new RuntimeException("Clock moved backwards. Refusing to generate id");
        }

        // 同一毫秒内，序列号自增
        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & $this->sequenceMask;
            if ($this->sequence == 0) {
                $timestamp = $this->nextTimestamp($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        // 位运算组合成64位ID
        return (($timestamp - $this->epoch) << $this->timestampLeftShift)
            | ($this->datacenterId << $this->datacenterIdShift)
            | ($this->workerId << $this->workerIdShift)
            | $this->sequence;
    }
}

class UniquenessId
{
    private string $res;

    public function __construct(string $prefix = "S_")
    {
        $worker = new SnowFlakeId(1, 1, 10);
        $this->res = $prefix . (string)$worker->nextId();
    }

    public function __toString(): string
    {
        return $this->res;
    }

    public function getId(): string
    {
        return $this->res;
    }
}