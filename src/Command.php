<?php namespace Console;

use Console\Models\CSV;
use Console\Services\GenerateFileService;
use Console\Services\SendEmailService;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Author: Chidume Nnamdi <kurtwanger40@gmail.com>
 */
class Command extends SymfonyCommand
{

    protected GenerateFileService $generateFileService;
    protected SendEmailService $sendEmailService;

    public function __construct(GenerateFileService $generateFileService, SendEmailService $sendEmailService)
    {
        parent::__construct();
        $this->generateFileService = $generateFileService;
        $this->sendEmailService = $sendEmailService;
    }

    protected function test(InputInterface $input, OutputInterface $output): void
    {
        $output->write($this->process($input->getArgument('email'), $input->getArgument('format')));
    }

    private function process(string $email, string $format = 'csv'): string
    {
        try{
            //ini header
            $newDataHeader = CSV::getHeader();

            //ini data
            $newData = [];

            //try to read large file rather than using file_get_contents
            if ($file = @fopen("https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl", "r")) {
                while (($line = fgets($file, 1024 * 8)) !== false) {
                    $dataObj = json_decode($line);
                    $total = $this->generateFileService->getOrderTotal($dataObj->items);
                    //check if total not 0
                    if ($total > 0)
                        $totalUnits = $this->generateFileService->getTotalUnitsCount($dataObj->items);
                    $newData[] = [
                        $dataObj->order_id,
                        $this->generateFileService->convertTime($dataObj->order_date),
                        $this->generateFileService->getOrderTotalWithDiscount($total, $dataObj->discounts),
                        $this->generateFileService->getAverageUnitPrice($total, $totalUnits),
                        count($dataObj->items),
                        $totalUnits,
                        $dataObj->customer->shipping_address->state
                    ];
                }
                fclose($file);
            }

            $file = $this->generateFileService->generate($newDataHeader, $newData, $format);
            $this->sendEmailService->send($email, $file, $format);
            return "Done";

        } catch (\Exception $e) {
            echo $e->getMessage();
            return "Error happens";
        }
    }
}