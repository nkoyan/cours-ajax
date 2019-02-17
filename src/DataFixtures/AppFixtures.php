<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = [];

        // creaate Admin
        $user = new User();
        $user->setEmail('yoann.kergall@gmail.com')
            ->setPassword($this->encoder->encodePassword($user, '1111'));

        $manager->persist($user);

        $users[] = $user;

        // Create users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, '1111'));

            $manager->persist($user);
            $users[] = $user;
        }

        // create posts and likes
        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(6))
                ->setIntroduction($faker->paragraph())
                ->setContent('<p>' . join(',', $faker->paragraphs()) . '</p>');

            $manager->persist($post);

            for ($j = 0; $j < mt_rand(0, 10); $j++) {
                $like = new PostLike();
                $like->setPost($post)
                    ->setUser($faker->randomElement($users))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'));

                $manager->persist($like);
            }
        }

        $manager->flush();
    }
}
