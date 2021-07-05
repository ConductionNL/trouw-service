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
            'westFriesland' => $this->createMunicipality('West Friesland', 'Gemeente West Friesland', '002224444', 'info@westfriesland.nl'),
            'denBosch' => $this->createMunicipality('\'s Hertogenbosch', 'Gemeente \'s Hertogenbosch', '001709124', 'info@denbosch.nl'),
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
            $group['products'][] = "/products/".$product['id'];
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
        $group = $this->addProductsToGroup($products, $group);

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
        $group = $this->addProductsToGroup($products, $group);

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
        $group = $this->addProductsToGroup($products, $group);

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
        $group = $this->addProductsToGroup($products, $group);

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
        $group = $this->addProductsToGroup($products, $group);

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
        $group = $this->addProductsToGroup($products, $group);

        return $this->commonGroundService->createResource($group, ['component' => 'pdc', 'type' => 'groups']);
    }

    public function createOffer(array $product, array $productRaw): array
    {

        $offer = [
            'name'          =>  $productRaw['name'],
            'description'   =>  $productRaw['description'],
            'price'         =>  $productRaw['price'],
            'priceCurrency' =>  $productRaw['priceCurrency'],
            'audience'      =>  $productRaw['audience'],
            'offeredBy'     =>  $product['sourceOrganization'],
            'products'      =>  ["/products/{$product['id']}"],
        ];

        return $this->commonGroundService->createResource($offer, ['component' => 'pdc', 'type' => 'offers']);
    }

    public function createProduct(array $productRaw, array $catalogue): array
    {
        $product = [
            'name'                  =>  $productRaw['name'],
            'description'           =>  $productRaw['description'],
            'catalogue'             =>  $catalogue['@id'],
            'sourceOrganization'    =>  $catalogue['sourceOrganization'],
            'type'                  =>  $productRaw['type'],
            'requiresAppointment'   =>  $productRaw['requiresAppointment'],
            'audience'              =>  $productRaw['audience'],
            'taxPercentage'         =>  $productRaw['taxPercentage'],
        ];

        $product = $this->commonGroundService->createResource($product, ['component' => 'pdc', 'type' => 'products']);
        $this->createOffer($product, $productRaw);

        return $product;
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
        $ambtenaren[] = $this->createProduct(
            [
                'name'                => 'Toegewezen Trouwambtenaar',
                'description'         => 'Uw trouwambtenaar wordt toegewezen, over enkele dagen krijgt u bericht van uw toegewezen trouwambtenaar!',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://dev.huwelijksplanner.online/images/content/ambtenaar/trouwambtenaar.jpg',
                'movie'               => 'https://www.youtube.com/embed/RkBZYoMnx5w',
            ],
            $catalogue
        );
        $ambtenaren[] = $this->createProduct(
            [
                'name'                => 'Stagair Trouwambtenaar',
                'description'         => 'Een stagair trouwambtenaar wordt aan uw huwelijk toegewezen.',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'internal',
                'logo'                => 'https://dev.huwelijksplanner.online/images/content/ambtenaar/trouwambtenaar.jpg',
                'movie'               => 'https://www.youtube.com/embed/RkBZYoMnx5w',
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
                'name'                => 'Balie',
                'description'         => '',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://www.utrecht.nl/fileadmin/uploads/documenten/9.digitaalloket/Burgerzaken/Trouwzaal-Stadskantoor-Utrecht.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
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
            ],
            $catalogue
        );
        $locations[] = $this->createProduct(
            [
                'name'                => 'Stadhuis kleine zaal',
                'description'         => 'Deze uiterst sfeervolle trouwzaal maakt de dag compleet',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://www.utrecht.nl/fileadmin/uploads/documenten/9.digitaalloket/Burgerzaken/kleine-trouwzaal-stadhuis-utrecht.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
        $locations[] = $this->createProduct(
            [
                'name'                => 'Stadhuis grote zaal',
                'description'         => 'Deze uiterst sfeervolle trouwzaal maakt de dag compleet',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://www.utrecht.nl/fileadmin/uploads/documenten/9.digitaalloket/Burgerzaken/grote-trouwzaal-stadhuis-utrecht.jpg',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
        $locations[] = $this->createProduct(
            [
                'name'                => 'Vrije locatie',
                'description'         => 'Vrije locatie',
                'type'                => 'simple',
                'price'               => '0.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'logo'                => 'https://user-images.githubusercontent.com/49227194/80487135-9baca180-895c-11ea-82a4-92967a1551c2.png',
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
        $extras[] = $this->createProduct(
            [
                'name'                => 'Ringen',
                'description'         => 'Het uitwisselen van ringen tijdens de huwelijksceremonie',
                'type'                => 'simple',
                'price'               => '10.00',
                'priceCurrency'       => 'EUR',
                'taxPercentage'       => 0,
                'requiresAppointment' => false,
                'audience'            => 'public',
                'movie'               => 'https://www.youtube.com/embed/DAaoMvj1Qbs',
            ],
            $catalogue
        );
        $extras[] = $this->createProduct(
            [
                'name'                => 'Geen extra\'s',
                'description'         => 'U wilt geen extra producten bij uw huwelijk',
                'type'                => 'simple',
                'price'               => '0.00',
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

    public function createProperty(array $stage): array
    {
        return $this->commonGroundService->createResource($stage, ['component' => 'vtc', 'type' => 'properties']);
    }

    public function createTask(array $resource): array
    {
        return $this->commonGroundService->createResource($resource, ['component' => 'vtc', 'type' => 'tasks']);
    }

    public function createRequestType($municipality): array
    {
        $requestType = [
            'name'         => 'Huwelijk / Partnerschap',
            'description'  => 'Huwelijk / Partnerschap',
            'organization' => $municipality['@id'],
            'icon'         => 'fal fa-rings-wedding',
        ];

        return $this->commonGroundService->createResource($requestType, ['component' => 'vtc', 'type' => 'request_types']);
    }

    public function createProperties($requestType): array
    {
        $properties = [];

        //stage 1
        $properties[] = $this->createProperty(
            [
                'start'  => true,
                'title'  => 'Type',
                'icon'   => 'fas fa-ring',
                'slug'   => 'ceremonie',
                'type'   => 'string',
                'format' => 'radio',
                'enum'   => [
                    'trouwen',
                    'partnerschap',
                    'omzetten',
                ],
                'required'    => true,
                'description' => 'Selecteer een huwelijk of partnerschap',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 2
        $properties[] = $this->createProperty(
            [
                'title'       => 'Partners',
                'icon'        => 'fas fa-user-friends',
                'slug'        => 'partner',
                'type'        => 'array',
                'format'      => 'url',
                'iri'         => 'irc/assents',
                'minItems'    => 2,
                'maxItems'    => 2,
                'required'    => true,
                'description' => 'Wie zijn de partners binnen dit huwelijk / partnerschap?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 3
        $properties[] = $this->createProperty(
            [
                'title'  => 'Plechtigheid',
                'icon'   => 'fas fa-glass-cheers',
                'slug'   => 'plechtigheid',
                'type'   => 'string',
                'format' => 'url',
                'iri'    => 'pdc/offer',
                'query'  => [
                    'audience'                          => 'public',
                    'products.groups.sourceOrganzation' => $requestType['organization'],
                    'products.groups.name'              => 'Ceremonies',
                ],
                'description' => 'Welke plechtigheid wenst u?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 4
        $properties[] = $this->createProperty(
            [
                'title'       => 'Datum',
                'icon'        => 'fas fa-calendar-day',
                'slug'        => 'datum',
                'type'        => 'string',
                'format'      => 'calendar',
                'description' => 'Selecteer een datum voor de voltrekking',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 5
        $properties[] = $this->createProperty(
            [
                'title'  => 'Locatie',
                'icon'   => 'fas fa-building',
                'slug'   => 'locatie',
                'type'   => 'string',
                'format' => 'url',
                'iri'    => 'pdc/offer',
                'query'  => [
                    'audience'                          => 'public',
                    'products.groups.sourceOrganzation' => $requestType['organization'],
                    'products.groups.name'              => 'Trouwlocaties',
                ],
                'description' => 'Waar wilt u de voltrekking laten plaatsvinden?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 6
        $properties[] = $this->createProperty(
            [
                'title'  => 'Ambtenaar',
                'icon'   => 'fas fa-user-tie',
                'slug'   => 'ambtenaar',
                'type'   => 'string',
                'format' => 'url',
                'iri'    => 'pdc/offer',
                'query'  => [
                    'audience'                          => 'public',
                    'products.groups.sourceOrganzation' => $requestType['organization'],
                    'products.groups.name'              => 'Trouwambtenaren',
                ],
                'description' => 'Door wie wilt u de plechtigheid laten voltrekken?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 7
        $properties[] = $this->createProperty(
            [
                'title'       => 'Getuigen',
                'icon'        => 'fas fa-users',
                'slug'        => 'getuige',
                'type'        => 'array',
                'format'      => 'url',
                'iri'         => 'irc/assents',
                'minItems'    => 2,
                'maxItems'    => 4,
                'description' => 'Wie zijn de getuigen?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 8
        $properties[] = $this->createProperty(
            [
                'title'  => 'Extras',
                'icon'   => 'fas fa-gift',
                'slug'   => 'extra',
                'type'   => 'array',
                'format' => 'url',
                'iri'    => 'pdc/offer',
                'query'  => [
                    'audience'                          => 'public',
                    'products.groups.sourceOrganzation' => $requestType['organization'],
                    'products.groups.name'              => 'Extra producten',
                ],
                'description' => 'Zijn er nog extra producten of diensten waar u gebruik van wilt maken?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 9
        $properties[] = $this->createProperty(
            [
                'title'  => 'Naamgebruik',
                'icon'   => 'fas fa-ring',
                'slug'   => 'naamgebruik',
                'type'   => 'string',
                'format' => 'radio',
                'enum'   => [
                    'geen wijziging',
                    'naam partner 1',
                    'naam partner 2',
                ],
                'description' => 'Welke achternaam wilt u gebruiken na de huwelijksvoltrekking?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 10
        $properties[] = $this->createProperty(
            [
                'title'  => 'Taal',
                'icon'   => 'fas fa-ring',
                'slug'   => 'taal',
                'type'   => 'string',
                'format' => 'radio',
                'enum'   => [
                    'Nederlands',
                    'Frans',
                    'Engels',
                ],
                'description' => 'In welke taal wilt u de plechtigheid voltrekken?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 11
        $properties[] = $this->createProperty(
            [
                'title'       => 'Opmerking',
                'icon'        => 'fas fa-envelope',
                'slug'        => 'opmerking',
                'type'        => 'string',
                'format'      => 'textarea',
                'description' => 'Heeft u nog opmerkingen die u graag wilt meegeven?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 12
        $properties[] = $this->createProperty(
            [
                'title'       => 'Melding',
                'icon'        => 'fas fa-envelope',
                'slug'        => 'melding',
                'type'        => 'boolean',
                'format'      => 'radio',
                'description' => 'Wilt u met deze reservering tevens uw melding voorgenomen huwelijks (her)indienen?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        //stage 12
        $properties[] = $this->createProperty(
            [
                'title'       => 'Betaling',
                'icon'        => 'fas fa-cash-register',
                'slug'        => 'betaling',
                'type'        => 'string',
                'format'      => 'url',
                'iri'         => 'bs/invoice',
                'description' => 'Hoe wilt u betalen?',
                'requestType' => '/request_types/'.$requestType['id'],
            ]
        );

        return $properties;
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

        return true;
    }

    public function createHuwelijkProcessType(array $municipality, array $requestType): array
    {
        $processType = [
//            'audience'              => 'public',
            'icon'                  => 'fal fa-rings-wedding',
            'sourceOrganization'    => $municipality['@id'],
            'name'                  => 'Huwelijk / Partnerschap',
            'description'           => 'Huwelijk / Partnerschap',
            'requestType'           => $requestType['@id'],
        ];

        return $this->commonGroundService->createResource($processType, ['component' => 'ptc', 'type' => 'process_types']);
    }

    public function createSection(array $resource): array
    {
        return $this->commonGroundService->createResource($resource, ['component' => 'ptc', 'type' => 'sections']);
    }

    public function createSections(array $properties, array $stages): array
    {
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[0]['id']}",
            'properties'  => [$properties[0]['@id']],
            'name'        => 'Soort huwelijk',
            'description' => 'Trouwen of partnerschap',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[0]['id']}",
            'properties'  => [$properties[2]['@id']],
            'name'        => 'Soort ceremonie',
            'description' => 'Trouwen of partnerschap',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[0]['id']}",
            'properties'  => [$properties[1]['@id']],
            'name'        => 'Partner',
            'description' => 'Met wie wilt u trouwen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[1]['id']}",
            'properties'  => [$properties[4]['@id']],
            'name'        => 'Locatie',
            'description' => 'Waar wilt u trouwen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[1]['id']}",
            'properties'  => [$properties[5]['@id']],
            'name'        => 'Ambtenaar',
            'description' => 'Door wie wilt u getrouwd worden?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[2]['id']}",
            'properties'  => [$properties[3]['@id']],
            'name'        => 'Datum',
            'description' => 'Wanneer wilt u trouwen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[3]['id']}",
            'properties'  => [$properties[6]['@id']],
            'name'        => 'Getuigen',
            'description' => 'Wie zijn uw getuigen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[10]['@id']],
            'name'        => 'Contactgegevens',
            'description' => 'Wat zijn uw contactgegevens?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[8]['@id']],
            'name'        => 'Naamsgebruik',
            'description' => 'Wat zijn uw voorkeuren qua naamsgebruik?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[9]['@id']],
            'name'        => 'Taal',
            'description' => 'Bent u beiden de Nederlandse taal machtig?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[7]['@id']],
            'name'        => 'Extras',
            'description' => 'Wilt u nog extras toevoegen aan uw huwelijk?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[10]['@id']],
            'name'        => 'Opmerkingen',
            'description' => 'Heeft u nog opmerkingen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[11]['@id']],
            'name'        => 'Melding voorgenomen huwelijk',
            'description' => 'Wilt u meteen een melding voorgenomen huwelijk doen?',
        ]);
        $sections[] = $this->createSection([
            'stage'       => "/stages/{$stages[4]['id']}",
            'properties'  => [$properties[12]['@id']],
            'name'        => 'Betaling',
            'description' => 'Doe hier uw betaling',
        ]);

        return $sections;
    }

    public function createStages(array $processType): array
    {
        $stage = [
            'name'          => 'Hoe wilt u trouwen?',
            'icon'          => 'fal fa-users',
            'slug'          => 'huwelijk-ceremonie',
            'description'   => 'Hoe wilt u trouwen?',
            'process'   => "/process_types/{$processType['id']}",
        ];
        $stages[] = $this->commonGroundService->createResource($stage, ['component' => 'ptc', 'type' => 'stages']);

        $stage = [
            'name'          => 'Waar wilt u trouwen?',
            'icon'          => 'fal fa-users',
            'slug'          => 'ambtenaar-locatie',
            'description'   => 'Waar wilt u trouwen?',
            'process'   => "/process_types/{$processType['id']}",
        ];
        $stages[] = $this->commonGroundService->createResource($stage, ['component' => 'ptc', 'type' => 'stages']);

        $stage = [
            'name'          => 'Wanneer wilt u trouwen?',
            'icon'          => 'fal fa-users',
            'slug'          => 'datum',
            'description'   => 'Wanneer wilt u trouwen?',
            'process'   => "/process_types/{$processType['id']}",
        ];
        $stages[] = $this->commonGroundService->createResource($stage, ['component' => 'ptc', 'type' => 'stages']);

        $stage = [
            'name'          => 'Wie zijn uw getuigen?',
            'icon'          => 'fal fa-users',
            'slug'          => 'getuigen',
            'description'   => 'Wie zijn uw getuigen?',
            'process'   => "/process_types/{$processType['id']}",
        ];
        $stages[] = $this->commonGroundService->createResource($stage, ['component' => 'ptc', 'type' => 'stages']);

        $stage = [
            'name'          => 'Overige gegevens',
            'icon'          => 'fal fa-users',
            'slug'          => 'overig',
            'description'   => 'Overige gegevens',
            'process'   => "/process_types/{$processType['id']}",
        ];
        $stages[] = $this->commonGroundService->createResource($stage, ['component' => 'ptc', 'type' => 'stages']);

        return $stages;
    }

    public function createStage(array $resource): array
    {
        return $this->commonGroundService->createResource($resource, ['component' => 'ptc', 'type' => 'stages']);
    }

    public function loadPtcFixtures(array $municipality, array $requestType, array $properties): bool
    {
        $processType = $this->createHuwelijkProcessType($municipality, $requestType);
        $stages = $this->createStages($processType);
        $sections = $this->createSections($properties, $stages);

        return true;
    }

    public function loadWrcFixtures(): array
    {
        $municipalities = $this->createMunicipalities();
        $this->loadPdcFixtures($municipalities['utrecht']);
        $this->loadVtcFixtures($municipalities['utrecht']);
        $this->loadBegravenFixtures($municipalities['westFriesland']);
        $this->loadVerhuizenFixtures($municipalities['denBosch']);

        return $municipalities;
    }

    public function loadVtcFixtures($municipality): array
    {
        $requestType = $this->createRequestType($municipality);
        $properties = $this->createProperties($requestType);

        $this->loadPtcFixtures($municipality, $requestType, $properties);

        return $requestType;
    }

    public function createCalendar(array $resource): array
    {
        return $this->commonGroundService->createResource($resource, ['component' => 'arc', 'type' => 'calendars']);
    }

    public function createCemetery(array $resource): array
    {
        return $this->commonGroundService->createResource($resource, ['component' => 'grc', 'type' => 'cemeteries']);
    }

    public function loadBegravenFixtures($municipality)
    {
        //create calendar
        $calendar = $this->createCalendar([
            'organization' => $municipality['@id'],
            'name' => 'Begraven Calendar',
            'description' => 'Kalender voor het begraven proces',
            'timeZone' => 'CET'
        ]);

        //create catalogue
        $catalogue = [
            'name'               => 'Gemeente West Friesland',
            'sourceOrganization' => $municipality['@id'],
        ];

        $catalogue = $this->commonGroundService->createResource($catalogue, ['component' => 'pdc', 'type' => 'catalogues']);

        //create grave types
        $graveTypes = [];
        $graveTypes[] = $this->createProduct([
            'name' => 'Particulier graf 10 jaar',
            'description' => 'Particulier graf 10 jaar',
            'type' => 'simple',
            'requiresAppointment' => false,
            'audience' => 'public',
            'price'               => '544.10',
            'priceCurrency'       => 'EUR',
            'taxPercentage'       => 0,
        ], $catalogue);

        $graveTypes[] = $this->createProduct([
            'name' => 'Particulier graf 20 jaar',
            'description' => 'Particulier graf 20 jaar',
            'type' => 'simple',
            'requiresAppointment' => false,
            'audience' => 'public',
            'price'               => '1087.95',
            'priceCurrency'       => 'EUR',
            'taxPercentage'       => 0,
        ], $catalogue);

        $graveTypesGroup = [
            'name'               => 'Grafsoorten',
            'description'        => 'Alle Grafsoorten',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $graveTypesGroup = $this->addProductsToGroup($graveTypes, $graveTypesGroup);

        $graveTypesGroup = $this->commonGroundService->createResource($graveTypesGroup, ['component' => 'pdc', 'type' => 'groups']);

        //create grave articles
        $graveArticles = [];

        $graveArticles[] = $this->createProduct([
            'name' => 'Gebruik graflift',
            'description' => 'Gebruik graflift',
            'type' => 'simple',
            'requiresAppointment' => false,
            'audience' => 'public',
            'price'               => '0',
            'priceCurrency'       => 'EUR',
            'taxPercentage'       => 0,
        ], $catalogue);

        $graveArticles[] = $this->createProduct([
            'name' => 'Kist dalen',
            'description' => 'Kist dalen',
            'type' => 'simple',
            'requiresAppointment' => false,
            'audience' => 'public',
            'price'               => '0',
            'priceCurrency'       => 'EUR',
            'taxPercentage'       => 0,
        ], $catalogue);

        $graveArticlesGroup = [
            'name'               => 'Grafartikelen',
            'description'        => 'Alle Grafartikelen',
            'sourceOrganization' => $catalogue['sourceOrganization'],
            'catalogue'          => $catalogue['@id'],

        ];
        $graveArticlesGroup = $this->addProductsToGroup($graveArticles, $graveArticlesGroup);

        $graveArticlesGroup = $this->commonGroundService->createResource($graveArticlesGroup, ['component' => 'pdc', 'type' => 'groups']);

        //create grc cemetery
        $cemetery = $this->createCemetery([
            'reference' => 'Begraafplaats in Oosterleek',
            'organization' => $municipality['@id'],
            'calendar' => $calendar['@id'],
            'graveTypes' => $graveTypesGroup['@id']
        ]);

        //vtc fixtures
        $requestType = [
            'name'         => 'Begrafenisplanner',
            'description'  => 'Met dit verzoek kunt u een begrafenis plannen',
            'organization' => $municipality['@id'],
            'icon'         => 'fa fa-headstone',
        ];

        $requestType = $this->commonGroundService->createResource($requestType, ['component' => 'vtc', 'type' => 'request_types']);

        $properties = [];

        //0
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Gemeente',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'string',
            'iri' => 'wrc/organizations',
            'required' => true
        ]);

        //1
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Begraafplaats',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'query' => ['organization' => 'request.properties.gemeente'],
            'format' => 'uri',
            'iri' => 'grc/cemetery',
            'required' => true
        ]);

        //2
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Soort graf',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'query' => ['audience' => 'public', 'products.groups.name' => 'Grafsoorten', 'products.groups.sourceOrganization' => '{{ request.properties.gemeente }}'],
            'format' => 'uri',
            'iri' => 'pdc/offer',
            'required' => true
        ]);

        //3
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Kistmaat',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'enum' => ['De kist valt binnen de standaard afmetingen van 55cm bij 200cm.', 'De kist is groter dan de standaard afmetingen van 55cm bij 200cm.'],
            'format' => 'radio',
            'required' => true
        ]);

        //4
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Opmerkingen',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'textarea',
            'required' => false
        ]);

        //5
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Datum',
            'icon' => 'fas fa-calendar-day',
            'type' => 'string',
            'format' => 'calendar',
            'description' => 'Selecteer een datum voor de voltrekking',
            'required' => true,
            'maxDate' => 'P21D',
            'configuration' => [
                'startingHour' => 'P9H',
                'endingHour' => 'P17H',
                'optionDuration' => 'P2H'
            ]
        ]);

        //6
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Artikelen',
            'icon' => 'fa fa-headstone',
            'type' => 'array',
            'query' => ['audience' => 'public', 'products.groups.name' => 'Grafartikelen', 'products.groups.sourceOrganization' => '{{ request.properties.gemeente }}'],
            'format' => 'uri',
            'iri' => 'pdc/offer',
            'required' => false
        ]);

        //7
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Grafnummer of grafnaam',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'text',
            'description' => 'In het geval van een bijzetting dient u het graf waarin dient te worden bijgezet te identificeren met een grafnummer of naam van reeds geplaatste overledenen',
            'required' => false
        ]);

        //8
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Aanvrager / Rechthebbende',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'irc/assents',
            'required' => true,
            'maxItems' => 1,
        ]);

        //9
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Contactpersoon',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'cc/people',
            'required' => true,
        ]);

        //10
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Factuur persoon',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'cc/people',
            'description' => 'Naar wie moet de factuur worden gestuurd voor deze begrafenis',
            'configuration' => [
                'email' => true,
                'telephone' => true,
                'givenName' => true,
                'familyName' => true,
                'address' => true,
            ]
        ]);

        //11
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Factuur adres',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'cc/addresses',
            'description' => 'Naar wie moet de factuur worden gestuurd voor deze begrafenis',
        ]);

        //12
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Overledene met bsn',
            'name' => 'overledeneBsn',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'string',
            'iri' => 'brp/ingeschrevenpersoon',
        ]);

        //13
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Overledene zonder bsn',
            'name' => 'overledeneNoBsn',
            'icon' => 'fa fa-headstone',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'cc/people',
            'configuration' => [
                'email' => false,
                'telephone' => false,
                'givenName' => true,
                'familyName' => true,
                'birthday' => true,
                'birthplace' => true
            ]
        ]);

        //ptc fixtures

        $processType = [
            'icon'                  => 'fas fa-monument',
            'login'                 => 'always',
            'name'                  => 'Aanvragen begrafenis',
            'description'           => 'Plan een begrafenis op een gekozen begraafplaats',
            'sourceOrganization'    => $municipality['@id'],
            'requestType'           => $requestType['@id'],
        ];

        $processType =  $this->commonGroundService->createResource($processType, ['component' => 'ptc', 'type' => 'process_types']);

        //gemeente stage
        $stage = $this->createStage([
            'name' => 'Gemeente',
            'orderNumber' => 1,
            'slug' => 'gemeente',
            'description' => 'De gemeente waarin de begrafenis plaats moet vinden',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[0]['@id']],
            'name'        => 'Gemeente',
            'description' => 'In welke gemeente wilt u iemand begraven?',
        ]);

        //begraafplaats stage
        $stage = $this->createStage([
            'name' => 'Begraafplaats',
            'orderNumber' => 2,
            'slug' => 'begraafplaats',
            'description' => 'Bij welke begraafplaats moet de begrafenis plaatsvinden',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[1]['@id']],
            'name'        => 'Begraafplaats',
            'description' => 'Bij welke begraafplaats moet de begrafenis plaatsvinden?',
        ]);

        //grafsoort stage
        $stage = $this->createStage([
            'name' => 'Grafsoort',
            'orderNumber' => 3,
            'slug' => 'grafsoort',
            'description' => 'Het soort graf waarin de overledene wordt begraven',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[2]['@id']],
            'name'        => 'Soort graf',
            'description' => 'Wat voor soort graf wilt u iemand in begraven?',
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[3]['@id']],
            'name'        => 'Kistmaat',
            'description' => 'Valt de kist binnen de standaard afmetingen van 55cm bij 200cm?',
        ]);

        //bestaand graf stage
        $stage = $this->createStage([
            'name' => 'Bestaand graf',
            'orderNumber' => 4,
            'slug' => 'bestaand-graf',
            'description' => 'Moet de overledene in een bestaand of een nieuw graf worden begraven?',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[7]['@id']],
            'name'        => 'Bestaand graf',
            'description' => 'In het geval van een bijzetting dient u het graf waarin dient te worden bijgezet te identificeren met een grafnummer of naam van reeds geplaatste overledenen',
        ]);

        //datum stage
        $stage = $this->createStage([
            'name' => 'Datum',
            'orderNumber' => 5,
            'slug' => 'datum',
            'description' => 'Wanneer gaat het afscheid plaatsvinden?',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[5]['@id']],
            'name'        => 'Datum',
            'description' => 'Wanneer vindt het afscheid plaats?',
        ]);

        //artikelen stage
        $stage = $this->createStage([
            'name' => 'Artikelen',
            'orderNumber' => 6,
            'slug' => 'artikelen',
            'description' => 'Selecteer hier de gewenste artikelen voor de begrafenis.',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[6]['@id']],
            'name'        => 'Artikelen',
            'description' => 'Selecteer hier de gewenste artikelen voor de begrafenis.',
        ]);

        //overledene stage
        $stage = $this->createStage([
            'name' => 'Overledene',
            'orderNumber' => 7,
            'slug' => 'overledene',
            'description' => 'Wie wordt er begraven?',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[12]['@id']],
            'name'        => 'Overledene met bsn',
            'description' => 'Wie is er overleden?',
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[13]['@id']],
            'name'        => 'Overledene zonder bsn',
            'description' => 'Wie is er overleden?',
        ]);

        //Aanvrager / Rechthebbende stage
        $stage = $this->createStage([
            'name' => 'Aanvrager / Rechthebbende',
            'orderNumber' => 8,
            'slug' => 'aanvrager-rechthebbende',
            'description' => 'Wie treed op als aanvrager/rechthebbende?',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[8]['@id']],
            'name'        => 'Aanvrager / Rechthebbende',
            'description' => 'Wie treed er op als aanvrager/rechthebbende?',
        ]);

        //Contactpersoon stage
        $stage = $this->createStage([
            'name' => 'Contactpersoon',
            'orderNumber' => 9,
            'slug' => 'contactpersoon',
            'description' => 'Wie treed op als contactpersoon?',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[9]['@id']],
            'name'        => 'Contactpersoon',
            'description' => 'Wie treed er op als contactpersoon?',
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[10]['@id']],
            'name'        => 'Factuuradres',
            'description' => 'Wie moet de factuur ontvangen?',
        ]);

        //aanvullende informatie stage
        $stage = $this->createStage([
            'name' => 'Aanvullende informatie',
            'orderNumber' => 10,
            'slug' => 'aanvullende-informatie',
            'process'   => "/process_types/{$processType['id']}",
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'properties'  => [$properties[4]['@id']],
            'name'        => 'Opmerkingen',
            'description' => 'Zijn er extra opmerkingen of wensen die u wilt meegeven?',
        ]);

    }

    public function loadVerhuizenFixtures($municipality)
    {
        //vtc fixtures
        $requestType = [
            'name'         => 'Verhuizen',
            'description'  => 'Met dit verzoek kunt u een verhuizing melden',
            'organization' => $municipality['@id'],
            'icon'         => 'fa fa-building',
        ];

        $requestType = $this->commonGroundService->createResource($requestType, ['component' => 'vtc', 'type' => 'request_types']);

        $properties = [];

        //0
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Datum',
            'icon' => 'fas fa-calendar-day',
            'type' => 'string',
            'format' => 'date',
            'description' => 'Wat is de verhuisdatum?',
            'utter' => 'Wat is de verhuisdatum?'
        ]);

        //1
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Adres',
            'description' => 'Wat is het nieuwe adres?',
            'utter' => 'Ik heb een vraag over uw nieuwe adres',
            'icon' => 'fas fa-map-marked',
            'type' => 'string',
            'format' => 'url',
            'iri' => 'bag/address',
            'required' => true
        ]);

        //2
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Wie',
            'icon' => 'fas fa-map-marked',
            'type' => 'array',
            'format' => 'bag',
            'required' => true,
            'description' => 'Wie gaat er verhuizen?',
            'utter' => 'Welke personen gaan er verhuizen?'
        ]);

        //3
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Wiebsn',
            'icon' => 'fas fa-map-marked',
            'type' => 'array',
            'format' => 'bsn',
            'description' => 'BSN nummers van alle verhuisenden',
        ]);

        //4
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'name' => 'shertogenboschEmail',
            'title' => 'Email',
            'icon' => 'fas fa-envelope',
            'type' => 'string',
            'format' => 'email',
            'description' => 'Op welk e-mail adres kunnen we u bereiken?',
            'utter' => 'Op welk e-mail adres kunnen we je bereiken als we vragen hebben over deze verhuizing?',
        ]);

        //5
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Telefoon',
            'icon' => 'fas fa-phone',
            'type' => 'string',
            'format' => 'tel',
            'description' => 'Op welk telefoonnummer kunnen we u bereiken?',
            'utter' => 'Op welk telefoonnummer kunnen we je bereiken als we vragen hebben over deze verhuizing?',
        ]);

        //6
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'notificatie',
            'icon' => 'fas fa-bell',
            'type' => 'boolean',
            'format' => 'radio',
            'description' => 'Mogen wij andere op de hoogte stellen van uw verhuizing?',
            'utter' => 'Mogen wij deze verhuizing aan anderen doorgeven? Bijvoorbeeld aan postdiensten, sportverenigingen of kerkgenootschappen?'
        ]);

        //7
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Artikelen',
            'icon' => 'fa fa-headstone',
            'type' => 'array',
            'query' => ['audience' => 'public', 'products.groups.name' => 'Grafartikelen', 'products.groups.sourceOrganization' => '{{ request.properties.gemeente }}'],
            'format' => 'uri',
            'iri' => 'pdc/offer',
        ]);

        //8
        $properties[] = $this->createProperty([
            'requestType' => '/request_types/'.$requestType['id'],
            'title' => 'Mee verhuizers',
            'icon' => 'fas fa-map-marked',
            'type' => 'string',
            'format' => 'meeverhuizen',
            'description' => 'Zijn er mensen die mee verhuizen?',
        ]);

        //9
        $properties[] = $this->createTask([
            'requestType' => '/request_types/'.$requestType['id'],
            'name' => 'Updaten burger service nummers',
            'description' => 'Deze task roept een webhook aan als er een verzoek van het type verhuizen wordt gecrieërd?',
            'endpoint' => $this->commonGroundService->cleanUrl(['component'=>'vs', 'type'=>'webhook']),
            'type' => 'get',
            'code' => 'set_bsn',
            'event' => 'create',
            'timeInterval' => 'P0D',
        ]);


        //ptc fixtures

        $processType = [
            'icon'                  => 'fas fa-truck-moving',
            'login'                 => 'onSubmit',
            'name'                  => 'Verhuizen',
            'description'           => 'Hier kunt u uw verhuizing doorgeven.',
            'sourceOrganization'    => $municipality['@id'],
            'requestType'           => $requestType['@id'],
        ];

        $processType =  $this->commonGroundService->createResource($processType, ['component' => 'ptc', 'type' => 'process_types']);

        // Waarheen en Wanneer stage
        $stage = $this->createStage([
            'name' => 'Waarheen en Wanneer',
            'description' => 'Waarheen en waneer wilt u verhuizen',
            'icon' => 'fas fa-calendar',
            'slug' => 'gegevens',
            'process' => '/process_types/' . $processType['id'],
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'name'        => 'Datum en tijd',
            'description' => 'Datum en tijd van de verhuizing',
            'properties'  => [
                $properties[0]['@id'],
                $properties[1]['@id'],
            ],
        ]);

        // contact gegevens stage
        $stage = $this->createStage([
            'name' => 'Contact Gegevens',
            'description' => 'Hoe kunnen wij u bereiken',
            'icon' => 'fas fa-calendar',
            'slug' => 'contact',
            'process' => '/process_types/' . $processType['id'],
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'name'        => 'Gegevens',
            'description' => 'Waar kunnen wij u bereiken als we vragen hebben over deze verhuizing',
            'properties'  => [
                $properties[4]['@id'],
                $properties[5]['@id'],
            ],
        ]);

        $this->createSection([
            'stage'       => "/stages/{$stage['id']}",
            'name'        => 'Notificatie',
            'description' => 'Mogen wij andere op de hoogste stellen van uw verhuizing?',
            'properties'  => [
                $properties[6]['@id']
            ],
        ]);

//        // live!2021 gegevens stage
//        $stage = $this->createStage([
//            'name' => 'LIVE!2021',
//            'description' => 'Deze stap zit normaal gesproken niet in de klantreis maar om je de inlog gegevens te kunnen e-mailen voor het zaaksysteem moeten we je wel kunnen bereiken! (oh en vergeet de gratis tompouce niet die je krijgt als je de zaak netjes afrond in het zaaksysteem!)',
//            'icon' => 'fas fa-users',
//            'slug' => 'bedrijfgegevens',
//        ]);
//
//        $this->createSection([
//            'stage'       => "/stages/{$stage['id']}",
//            'name'        => 'Contactgegevens voor de tompouce',
//            'description' => 'Wat zijn uw contactgegevens?',
//            'properties'  => [
//            ],
//            'process' => '/process_types/' . $processType['id'],
//        ]);
    }
}
