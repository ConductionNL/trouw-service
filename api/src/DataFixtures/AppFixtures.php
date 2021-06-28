<?php

namespace App\DataFixtures;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    private ParameterBagInterface $parameterBag;
    private CommonGroundService $commonGroundService;

    public function __construct(ParameterBagInterface $parameterBag, CommonGroundService $commonGroundService)
    {
        $this->parameterBag = $parameterBag;
        $this->commonGroundService = $commonGroundService;
    }

    public function load(ObjectManager $manager)
    {
        if ($this->parameterBag->get('app_build_all_fixtures') == 'false' || !$this->parameterBag->get('app_build_all_fixtures')) {
            $this->loadWrcFixtures();
        }
    }

    public function createMunicipality(string $name, string $description, string $rsin, string $email): array
    {
        $contact = [
            'name'        => $name,
            'description' => $description,
            'emails'      => [
                [
                    'name'  => "General e-mail for $name",
                    'email' => $email,
                ],
            ],
        ];
        $contact = $this->commonGroundService->createResource($contact, ['component' => 'cc', 'type' => 'organizations']);

        $municipality = [
            'name'        => $name,
            'description' => $description,
            'rsin'        => $rsin,
            'contact'     => $contact['@id'],
        ];

        return $this->commonGroundService->createResource($municipality, ['component' => 'wrc', 'type' => 'organizations']);
    }

    public function createMunicipalities(): array
    {
        return [
            'utrecht' => $this->createMunicipality('Utrecht', 'Gemeente Utrecht', '002220647', 'info@utrecht.nl'),
        ];
    }

    public function createCatalogue(array $municipality): array
    {
        $catalogue = [
            'name'               => 'Gemeente Utrecht',
            'sourceOrganization' => $municipality['@id'],
        ];

        return $this->commonGroundService->createResource($catalogue, ['component' => 'pdc', 'type' => 'catalogues']);
    }

    public function addProductsToGroup(array $products, array $group): array
    {
        foreach ($products as $product) {
            $group['products'][] = $product['@id'];
        }

        return $group;
    }

    public function createBurgerzakenGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'               => 'Burgerzaken',
            'description'        => 'Alle producten met betrekking tot burgerzaken',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createTrouwproductenGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'               => 'Trouwproducten',
            'description'        => 'Alle producten met betrekking tot burgerzaken',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createTrouwambtenarenGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'        => 'Trouwambtenaren',
            'description' => '<p>Een trouwambtenaar heet officieel een buitengewoon ambtenaar van de burgerlijke stand (babs ). Een babs waarmee het klikt is belangrijk. Hieronder stellen de babsen van de gemeente Utrecht zich aan u voor. U kunt een voorkeur aangeven voor een van hen, dan krijgt u data te zien waarop die babs beschikbaar is. Wanneer u een babs heeft gekozen zal deze na de melding voorgenomen huwelijk, zelf contact met u opnemen.</p>

                <p>Kiest u liever voor een babs uit een andere gemeente? Of voor een vriend of familielid als trouwambtenaar? Dan kunt u hem of haar laten benoemen tot trouwambtenaar voor 1 dag bij de gemeente Utrecht. Dit kunt u hier ook opgeven.</p>

                <p>Bij een gratis of een eenvoudig huwelijk of geregistreerd partnerschap kunt u niet zelf een babs kiezen, de gemeente wijst er een toe.</p>',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createTrouwlocatiesGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'        => 'Trouwlocaties',
            'description' => '<p>Een trouwlocatie; in Utrecht is er voor elk wat wils. De gemeente Utrecht heeft een aantal eigen trouwlocaties; het Stadhuis, het Wijkservicecentrum in Vleuten en het Stadskantoor. Een keuze voor een van deze trouwlocaties kunt u direct hier doen.</p>

                <p>Daarnaast zijn er verschillende andere vaste trouwlocaties. Deze trouwlocaties zijn door de gemeente Utrecht al goedgekeurd. Hieronder vindt u het overzicht van deze trouwlocaties. Heeft u een keuze gemaakt uit een van de vaste trouwlocaties? Maak dan eerst een afspraak met de locatie en geef dan aan ons door waar en wanneer u wilt trouwen.</p>

                <p>Maar misschien wilt u een heel andere locatie. Bijvoorbeeld het caf&eacute; om de hoek, bij u thuis of in uw favoriete restaurant. Zo\'n locatie heet een vrije locatie. Een aanvraag voor een vrije locatie kunt u hier ook doen.</p>',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createCeremoniesGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'               => 'Ceremonies',
            'description'        => 'Verschillende ceremonies voor uw huwelijk / partnerschap',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],
        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createExtrasGroup(array $catalogue, array $products): array
    {
        $group = [
            'name'               => 'Extra producten',
            'description'        => 'Extra producten voor bij uw huwelijk',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createProduct(array $product, array $catalogue): array
    {
        $product['catalogue'] = $catalogue['@id'];
        $product['sourceOrganization'] = $catalogue['sourceOrganization'];

        return $this->commonGroundService->createResource($product, ['component' => 'pdc', 'type' => 'products']);
    }

    public function createCeremonies(array $catalogue): array
    {
        $ceremonies = [];
        $ceremonies[] = $this->createProduct(
            [
                'name'        => 'Uitgebreid trouwen',
                'description' => 'Mogelijk op een door u gekozen dag en tijdstip.<br>
U trouwt in één van de beschikbare locaties. Een eigen locatie is ook mogelijk.<br>
De trouwambtenaar houdt een toespraak en heeft vooraf contact met u.<br>
Een eigen trouwambtenaar (reeds beëdigd of nog niet beëdigd) is ook mogelijk,',
                'type'                => 'set',
                'price'               => '627.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
            ],
            $catalogue
        );
        $ceremonies[] = $this->createProduct(
            [
                'name'        => 'Eenvoudig Trouwen',
                'description' => 'Mogelijk op maandag 11.00 uur en 11.30 uur en dinsdag, woensdag en vrijdag om 10.00 uur, 10.30 uur, 11.00 uur of 11.30 uur.<br>
U trouwt zonder ceremonie (5-10 minuten).<br>
U trouwt in een kleine trouwruimte op de 6e etage van het stadskantoor.<br>
Er kunnen maximaal 10 personen naar binnen, dit is inclusief het bruidspaar, de getuigen en een eventuele fotograaf.<br>
De trouwambtenaar houdt geen toespraak en heeft vooraf geen contact met u.<br>
De wachtlijst voor eenvoudig trouwen is ongeveer 3 maanden.<br>
Een afspraak voor eenvoudig en gratis trouwen kan pas worden gemaakt als u uw voorgenomen huwelijk al gemeld hebt.',
                'type'                => 'set',
                'price'               => '163.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
            ],
            $catalogue
        );
        $ceremonies[] = $this->createProduct(
            [
                'name'        => 'Gratis Trouwen',
                'description' => 'Maandagochtend om 10.00 uur of om 10.30 uur kunt u gratis trouwen op het stadskantoor.<br>
De wachtlijst voor gratis trouwen is ongeveer 9 maanden.<br>
U trouwt zonder ceremonie (5-10 minuten).<br>
U trouwt in een kleine trouwruimte op de 6e etage van het stadskantoor.<br>
Er kunnen maximaal 10 personen naar binnen, dit is inclusief het bruidspaar, de getuigen en een eventuele fotograaf.<br>
De trouwambtenaar houdt geen toespraak en heeft vooraf geen contact met u.<br>
Een afspraak voor eenvoudig en gratis trouwen kan pas worden gemaakt als u uw voorgenomen huwelijk al gemeld hebt.',
                'type'                => 'set',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
            ],
            $catalogue
        );
        $ceremonies[] = $this->createProduct(
            [
                'name'                => 'Flitshuwelijk',
                'description'         => '',
                'type'                => 'set',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
            ],
            $catalogue
        );

        return $ceremonies;
    }

    public function createAmbtenaren(array $catalogue): array
    {
        $ambtenaren = [];
        $ambtenaren[] = $this->createProduct(
            [
                'name'                => 'Dhr Erik Hendrik',
                'description'         => '<p>Als Buitengewoon Ambtenaar van de Burgerlijke Stand geef ik, in overleg met het bruidspaar, invulling aan de huwelijksceremonie.</p>',
                'type'                => 'person',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://huwelijksplanner.online/images/content/ambtenaar/erik.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
        $ambtenaren[] = $this->createProduct(
            [
                'name'                => 'Mvr Ike van den Pol',
                'description'         => '<p>Elkaar het Ja-woord geven, de officiële ceremonie. Vaak is dit het romantische hoogtepunt van de trouwdag. Een bijzonder moment, gedeeld met de mensen die je lief zijn. Een persoonlijke ceremonie, passend bij jullie relatie. Alles is bespreekbaar en maatwerk. Een originele trouwplechtigheid waar muziek, sprekers en kinderen een rol kunnen spelen. Een ceremonie met inhoud, ernst en humor, een traan en een lach, stijlvol, spontaan en ontspannen.</p>',
                'type'                => 'person',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://huwelijksplanner.online/images/content/ambtenaar/ike.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
        $ambtenaren[] = $this->createProduct(
            [
                'name'                => 'Dhr. Rene Gulje',
                'description'         => '<p>Ik ben Rene Gulje, in 1949 in Amsterdam geboren. Ik studeerde Nederlands aan de UVA en journalistiek aan de HU.</p>',
                'type'                => 'person',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://huwelijksplanner.online/images/content/ambtenaar/rene.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );

        return $ambtenaren;
    }

    public function createLocations(array $catalogue): array
    {
        $locations = [];
        $locations[] = $this->createProduct(
            [
                'name'        => 'Stadskantoor',
                'description' => 'Deze locatie is speciaal voor eenvoudige en gratis huwelijken.
                     De zaal ligt op de 6e etage van het Stadskantoor.
                     De ruimte is eenvoudig en toch heel intiem.
                     Het licht is in te stellen op een kleur die jullie graag willen.',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://huwelijksplanner.online/images/content/ambtenaar/erik.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );

        return $locations;
    }

    public function createExtras(array $catalogue): array
    {
        $extras = [];
        $extras[] = $this->createProduct(
            [
                'name'                => 'Trouwboekje',
                'description'         => 'Een mooi in leer gebonden herinnering aan uw huwelijk',
                'type'                => 'simple',
                'price'               => '30.20',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );

        return $extras;
    }

    public function loadPdcFixtures(array $municipality): bool
    {
        $catalogue = $this->createCatalogue($municipality);
        $ceremonies = $this->createCeremonies($catalogue);
        $ceremoniesGroup = $this->createCeremoniesGroup($catalogue, $ceremonies);

        $ambtenaren = $this->createAmbtenaren($catalogue);
        $ambtenarenGroup = $this->createTrouwambtenarenGroup($catalogue, $ambtenaren);

        $locations = $this->createLocations($catalogue);
        $locationsGroup = $this->createTrouwlocatiesGroup($catalogue, $locations);

        $extras = $this->createExtras($catalogue);
        $extrasGroup = $this->createExtrasGroup($catalogue, $extras);

        $products = array_merge($ceremonies, $ambtenaren, $locations, $extras);
        $this->createTrouwproductenGroup($catalogue, $products);
        $this->createBurgerzakenGroup($catalogue, $products);
    }

    public function loadWrcFixtures(): array
    {
        $municipalities = $this->createMunicipalities();
        $this->loadPdcFixtures($municipalities['utrecht']);
    }
}
