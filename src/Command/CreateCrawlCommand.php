<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Service\CrawlService;

/*******/
/* Commande qui lance le crawl d'une recherche en recuperant l'id
/********/

class CreateCrawlCommand extends Command
{
	private $crawlService;

	public function __construct(CrawlService $crawlService){
		$this->crawlService = $crawlService;
		parent::__construct();
	}
    protected function configure()
    {
         $this
        // the name of the command (the part after "bin/console")
        ->setName('app:crawl')

        // the short description shown while running "php bin/console list"
        ->setDescription('Crawl a research.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to crawl a research.')
        ->addArgument('id', InputArgument::REQUIRED, 'What is the search ID?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->crawlService->crawlIndex2($input->getArgument('id'));
    }
}