<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\EventDetail;
use App\Entity\Product;
use App\Entity\SiteEvent;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create("fr_FR");

        for ($i=0; $i < 10 ; $i++) { 

        $product = new Product();
        $product->setName($faker->title);
        $product->setCategory($faker->randomElement(['éléctricité', 'mobilier', 'lustre', 'chapiteaux', 'décoration']));
        $product->setStockInitial($faker->numberBetween(1, 100));
        $product->setStock($faker->numberBetween(1, 100));
        $product->setHs($faker->numberBetween(1, 20));
        $product->setLost($faker->numberBetween(1, 20));
        $manager->persist($product);


        //CONTACT
        $contact= new Contact();
        $contact->setFirstName($faker->firstName);
        $contact->setLastName($faker->lastName);
        $contact->setPhoneNumber(substr($faker->phoneNumber, 0, 14));
        $contact->setEmail($faker->email);
        $contact->setStatus($faker->randomElement(['client', 'sous traitent', 'fournisseur', 'interne']));
        $manager->persist($contact);


        //EVENT
        $siteEvent= new SiteEvent();
        $siteEvent->setName($faker->name);
        $siteEvent->setAddress($faker->address);
        $siteEvent->setCity($faker->city);
        $siteEvent->setPostalCode($faker->postcode);
        $siteEvent->setDescription($faker->text);
        $manager->persist($siteEvent);

        // USER
        $user = new User();
        $user->setFirstName($faker->firstName);
        $user->setLastName($faker->lastName);
        $user->setEmail($faker->email);
        $user->setPassword($faker->password);
        $user->setRoles([$faker->randomElement(['ROLE_ADMIN', 'ROLE_USER'])]);
        $user->setDateEntry($faker->dateTime);
        $user->setIsVerified($faker->boolean);
        $manager->persist($user);

        //Event
        $event = new Event();
        $event->setSite($siteEvent);
        $event->setContact($contact);
        $event->setDateMontage($faker->dateTime);
        $event->setDateStartShow($faker->dateTime);
        $event->setDateEndShow($faker->dateTime);
        $event->setDateEnd($faker->dateTime);
        $manager->persist($event);

        //EVENTDETAIL
        $eventDetail = new EventDetail();
        $eventDetail->setEvent($event);
        $eventDetail->setProduct($product);
        $eventDetail->setUser($user);
        $eventDetail->setMouve($faker->randomElement(['new', 'preparer', 'livrer', 'retour']));
        $eventDetail->setQuantity($faker->numberBetween(1, 100));
        $eventDetail->setDate($faker->dateTime);
        $manager->persist($eventDetail);

        }



        $manager->flush();
    }
}
