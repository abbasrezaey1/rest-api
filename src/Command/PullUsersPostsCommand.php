<?php

namespace App\Command;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:pull-users-posts',
    description: 'Pulling users and posts from APIs.',
)]
class PullUsersPostsCommand extends Command
{
    private ContainerInterface $container;
    private HttpClientInterface $client;

    public function __construct(ContainerInterface $container, HttpClientInterface $client)
    {
        parent::__construct();

        $this->container = $container;
        $this->client = $client;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Getting users
        $users = $this->client
            ->request('GET', 'https://gorest.co.in/public/v2/users')
            ->toArray();

        $io = new SymfonyStyle($input, $output);
        $progressBar = new ProgressBar($output, count($users));
        $progressBar->start();

        // Saving users and posts data on database
        foreach ($users as $user) {
            // Getting posts
            $posts = $this->client
                ->request('GET', sprintf('https://gorest.co.in/public/v2/users/%s/posts', $user['id']))
                ->toArray();

            if (count($posts) === 0) {
                continue;
            }

            // Saving user
            $userId = $this->storeUser($user);
            $progressBar->advance();

            // Saving posts
            foreach ($posts as $post) {
                $this->storePost($post, $userId);
            }
        }

        $progressBar->finish();
        $io->success('Pulling data is completed.');
        return Command::SUCCESS;
    }

    /**
     * @param array $data
     * @return int
     */
    private function storeUser(array $data): int
    {
        $entityManager = $this->container->get('doctrine')?->getManager();

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setGender($data['gender']);
        $user->setStatus($data['status']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user->getId();
    }

    /**
     * @param array $data
     * @param int $userId
     * @return void
     */
    private function storePost(array $data, int $userId): void
    {
        $entityManager = $this->container->get('doctrine')?->getManager();

        $post = new Post();
        $post->setUserId($userId);
        $post->setTitle($data['title']);
        $post->setBody($data['body']);

        $entityManager->persist($post);
        $entityManager->flush();
    }
}
