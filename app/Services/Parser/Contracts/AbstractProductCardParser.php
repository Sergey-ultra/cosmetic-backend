<?php
declare(strict_types=1);


namespace App\Services\Parser\Contracts;

use App\Models\ParsingLink;
use App\Services\Parser\DTO\ParsingLinkWithOptionsDTO;
use App\Services\Parser\DTO\ProductCardDTO;
use App\Services\ProxyHttpClientService\ProxyHttpClientService;
use GuzzleHttp\Client;


abstract class AbstractProductCardParser
{
    protected string $brandClearValue = '';
    protected string $nameClearValue = '';
    protected string $ingredients = '';
    protected string $application = '';
    protected string $volume = '1pc';
    protected ProductCardDTO $productCard;


    public function __construct(protected ParsingLinkWithOptionsDTO $currentLink)
    {
        $this->productCard = new ProductCardDTO();
    }

    protected function getBody(): ?string
    {
        $body = $this->currentLink->body;
        if (!$body) {

            $httpClient = app(ProxyHttpClientService::class);
            $response = $httpClient->request($this->currentLink->link);
            $code = $response->getStatusCode();



            if ($code === 200) {
                $body = $response->getBody()->getContents();
                ParsingLink::where('id', $this->currentLink->id)->update(['body' => $body]);
            } else if ($code === 404) {
                ParsingLink::where('id', $this->currentLink->id)->update(['parsed' => 2]);
            }
        }
        return $body;
    }




    abstract public  function parseProductCard(): ?ProductCardDTO;

    abstract protected function getImages(): void;



//        $imgDom = $domElement->find($imgTag);
//        $imgDom = pq($imgDom)->find('span');
//        foreach ($imgDom as $span) {
//            $span = pq($span);
//
//            if ($span->attr('data-size') === "1600") {
//
//                $urlArray = parse_url($link);
//                $storeUrl =  $urlArray['scheme'] . '://' . $urlArray['host'];
//                $imgInfo = Image::saveImage($storeUrl, $span->attr('data-path'));
//                if (gettype($imgInfo) == 'array') {
//                    $this->info['images'][] = $imgInfo;
//                }
//            }
//        }




    abstract protected function extractValue(array $option): ?string;


    protected function clearValues(string $current, string $name, string $tag)
    {
        $volumePattern = '#(\d+\s+(х|x|\*)(\s+|\d+,)|\d+(\*\d+,|\*|х))\d+\s+([Мм][Лл]|[GgГг]|[Гг][Рр])|(\d+|\d+\s+)(капс|[Шш]т по \d+,d+\s+[Мм][Лл]|[Mm][Ll]|[GgГг](р?)|[Мм][Лл]|[Шш]т|капсул|capsules|(ампул|амп) по\s+\d+\s+[Мм][Лл])#u';
        $pricePattern = '#[^\d+]#';
        $applicationPattern = '#Способ применения.*#us';

        //$ingredientPattern = '#Состав:.*?(?=(Способ|Применение))#us';
        //$ingredientPattern = '#Активные ингредиенты. Состав:*?(\.)#us';
        //$ingredientPattern = '#Активные ингредиенты.*?(?=(Способ|Применение))#us';
        //$ingredientPattern = '#Состав:.*?(\.|\r\n)#us';
       // $ingredientPattern = '#Состав:.*?\.#us';
        $ingredientPattern = '#Состав:.*#us';




        switch ($name) {
            case 'price':
                $current = (int) preg_replace($pricePattern, '', $current);
                break;
            case "brand":
                $current = preg_replace('#\n#', '', $current);
                $current = trim($current);
                if (strpos($current, "ARAVIA PROFESSIONAL") !== false) {
                    $current = trim(str_replace('PROFESSIONAL', '', $current));
                }
                if (strpos($current, "GIGI COSMETIC LABS") !== false) {
                    $current = trim(str_replace('COSMETIC LABS', '', $current));
                }
                if (strpos($current, "INSPIRA COSMETICS") !== false) {
                    $current = trim(str_replace('COSMETICS', '', $current));
                }


                $this->brandClearValue = $current = str_replace('®', '', $current);
                break;
            case "country":
                $current = trim(preg_replace('#\r|\n|\t#', '', $current));
                if (preg_match('!\(([^\)]+)\)!', $current, $countryMatches)) {
                    $current = $countryMatches[1];
                }
                break;
            case "name":
                $current = trim($current);
                if (!preg_match('#[Нн]абор#', $current, $a)) {

                    if (preg_match($volumePattern, $current, $matches)) {
                        $this->volume = $matches[0];
                    }
                    $current = preg_replace($volumePattern, '', $current);

                    $current = trim(preg_replace('#\(\)|"#', '', $current));
                    $current = trim(preg_replace('#,,#', '', $current));
                    $current = trim(preg_replace('#/#', '', $current));
                    $current = trim($current, '.');
                    $current = trim($current);
                    $current = trim($current, ',');
                }

                $this->nameClearValue = implode(' ', array_filter(explode(',', $current)));
                $this->nameClearValue = preg_replace('#\s+/#', '', $this->nameClearValue);

                break;
            case "volume":
                if (preg_match($volumePattern, $current, $volumeMatches)) {
                    $current = $volumeMatches[0];
                }

                if ($tag === '' || $current === '') {
                    $current = $this->volume;
                }
                break;
            case 'description':
                //$current = preg_replace('#Активные ингредиенты.*?(?=Способ)#us', '', $current);
                if (true) {
                    if (preg_match($ingredientPattern, $current, $ingredientsMatches)) {
                        $current = preg_replace($ingredientPattern, '', $current);
                        $this->ingredients = $ingredientsMatches[0];
                    }

                    if (preg_match($applicationPattern, $current, $applicationMatches)) {
                        $current = preg_replace($applicationPattern, '', $current);
                        $this->application = $applicationMatches[0];
                    }
                }

                $current = trim(preg_replace('#\t#','',$current));
                break;
            case "ingredient":
                if ((!$current || !$tag) && $this->ingredients) {
                    $current = $this->ingredients;
                }
                if ($current) {

                    $current = html_entity_decode($current);
                    //dd($current);
                    $current = trim(preg_replace('#\r|\n|\t#', '', $current));
                    $current = trim(preg_replace('#Состав:#', '', $current));
                    $current = trim(preg_replace('#Активные ингредиенты.#', '', $current));
                    $current = trim(preg_replace('#Ингредиенты.#', '', $current));
                    $current = trim(preg_replace('#Ingredients:#', '', $current));
                    $current = trim(preg_replace('#INGREDIENTS:#', '', $current));
                    $current = trim(preg_replace('#&nbsp;#',' ', $current));
                    //$current = trim(preg_replace('#LIFTING PHASE:#', '', $current));
                    //$current = trim(preg_replace('#.\s+FIRMING PHASE:#', ',', $current));
                    //$current = trim(preg_replace('#99,4% OF INGREDIENTS OF NATURAL ORIGIN. NATURELLE:#', '', $current));
                    //$current = trim(preg_replace('#\. 0,6% of ingredients guaranteeing sensoriality and good conservation of the formula:#', ',', $current));


                    $current = trim(rtrim(trim($current), '.'));



                    $current = preg_replace('#\*#', '', $current);
                    $current = preg_replace("#,\xc2\xa0|,\s+|;\s+#", ',', $current);
                    //$current = preg_replace("#\xc2\xa0#", '', $current);

                    //$current = preg_replace("#\s+\(([^)]*?),([^(]*?)\)#", '', $current);


                    //$current =  preg_split('#(?<!,\d)(\s+,|,)#', $current);
                    $current =  preg_split('#(?<!,\d)(\s+,|,)(?!000ppm\))#', $current);
                    //$current =  preg_split('#(?<!,\d)(\s+,|,)(?!.?\))#us', $current);




//                        preg_match_all('#Neomatrix.*\Z|.*(?=Neomatrix)#', $current, $matches);
//                        $matches[0][0] = trim($matches[0][0]);
//                        $current = implode(', ', $matches[0]);


//                        preg_match_all('#[^,]*\(.*?\)[^,]*|[^,()]+#',  $current,$matches);

//                        $current =  $matches[0];


                    if (!is_array($current)) {
                        $current = [];
                    }
//                        foreach ($current as &$m) {
//                            $m = trim($m);
//                        }

                } else {
                    $current = [];
                }

                break;
            case 'application':
                $current = trim(preg_replace('#\r|\n|\t#', '', $current));

                if ($current === '' && $this->application) {
                    $current = $this->application;
                }
                break;
            case 'purpose':
                $current = trim(preg_replace('#\r|\n|\t#', '', $current));
                break;
            default:
                $current = trim(preg_replace('#\r|\n|\t#', '', $current));
        }

        return $current;
    }

}
