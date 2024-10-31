<?php

use PHPUnit\Framework\TestCase;
use Mockery as m;
use Doku\Snap\Snap;
use Doku\Snap\Models\CardRegistration\CardRegistrationRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationgResponseDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationAdditionalInfoRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationCardDataRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationAdditionalInfoResponseDto;

class DDCardRegistrationTest extends TestCase
{
    private $snap;
    private $mockTokenController;
    private $mockDirectDebitController;
    private string $privateKey = "-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC3EFZ9ahjQQLzq
9Nf3d9BbmDaMIEaFSsrgmxUUslWWKcpR3FWNT8anUm1p5imw3AcPDMwnc8P1f6Fy
25eWSSgqdIeKvP7T93b+ShYCkJeGnpPN6VWqfdmECt2cBxgpQtOjjqU1jUYG5sPX
fKCbFdE8D+BESzWHia/+EhpTFv4xmbaFhQtUjqhsMUNUEmr4GQY9UhSFGhHM+MVJ
T3MGJFKxkKLGnx0R7i9gP9eFE+wvFHbREzrgs/D5j1J2/o1oGSBeSzHj5CT3ZPNJ
Hg7aVvRkUptfqZQAfMHuoL804hICXQxYPfSSQnJuC2fpOtrJRedtPuAeDMWN/T7R
REEXsHvRAgMBAAECggEABvS7uJnAth8TnqDtmOVoWSakQfhS/asdIOI7r5TkLCfL
3IbxCETVAVQgPLMmd+YwbXm4wzICqvAt2BGwhaEjgdN6OAMc8rx3PMvfYLsSSub5
KkyYuPj9vCb2i0B4wk6cAJ3BuNJ0q/v2SpideK8gS3Y1+Rpbfxo0AgU5k1kvEP7c
C5KLeJpfJUsYzdROgz4Nm31N1f78b+xpmBk5BunVi/p5V88ffD8E0cXXvgGAOrll
StUZF6TcZRHWZm4uBwKzapDdRUJw4bfAs7KkPu1BjXYb+UQTJtf3QkGdPdkbpIAq
NqUTZHcpid69rs7WRHYbnpLesE5S/EnjsSTvKkqTAQKBgQDemRGgu3nGmTkItl/5
8RMe8EgKWrZ5bbrmifCV9sbBzSvCG/x67lmYH1nEQgb7P+cshSxExNx8oNMBKjo5
SbYD8t+brHrbAKpeMspIKituorSgKyAI78qmerDwETdVLjaptAeB2rtItDflKLhi
vvc2oMENrs9P6vwRaQBRL46lEQKBgQDSiJigOBrlZ7ktIYcgjhItj5ym2tBQZR3l
r3se8QCJBX5qkhHBr8ZW+RNbhVUIJOc6zS+FVkNXDjTujshXmCg2rvunRMeEY8lr
bqY6b1qU7Dy2ayUxOcCrMncyu0m+816sKea439IAbBsOBDpLfy3c9XVxRQNl/9jA
vkVlW/1qwQKBgEtG8oorvGPoHzyOCGkLGM9GrOYrhTgNXr5l+aGNYevaSakMM9cS
0eO6/m17csb29mO86ZqcBIB1FsZ3FFeZUN+G/A22R5nWYMcYYAYFlMiGZ5Ue4GeF
SEACj+GvwMmipkO/qSZF5T4SDDEIE0r5j7q+pGrPKja5neL5Ym0SKygxAoGAT0/Y
N5uLPlrp3r+fuTrsTC+q8wzBp1fAgJOwDUL7UaM5MYqfl9jRHlis2zCKjQvh9Dvy
KAgMTFJ0zF4LyTvwAlG/Sg9WHmC9M3S2uJLUi//HC22n0DiShav4TatSp9XlF3Sd
j435/eC7/HbJQ0HBFFmACdDW6+kLTekgGYQjHsECgYBMiwrH5sAV9bWa8M4cTZFj
rftc352gjDhdxFsUmU6oDBa5ErMaVqe+r/RiIMWO3a3PBoYyYqMRF5JB/pF+ZN7D
bEjWwooi232C/6fIDRnQtJs/huZm1msK410lJ8dsCG/5H+nuFpPiGQgHCwXiwSw+
ZMjguO6OOHnN77tklXQqCg==
-----END PRIVATE KEY-----";
private $issuer = "doku";
private $clientId = "BRN-0208-1720408264694";
private $publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtxBWfWoY0EC86vTX93fQ
W5g2jCBGhUrK4JsVFLJVlinKUdxVjU/Gp1JtaeYpsNwHDwzMJ3PD9X+hctuXlkko
KnSHirz+0/d2/koWApCXhp6TzelVqn3ZhArdnAcYKULTo46lNY1GBubD13ygmxXR
PA/gREs1h4mv/hIaUxb+MZm2hYULVI6obDFDVBJq+BkGPVIUhRoRzPjFSU9zBiRS
sZCixp8dEe4vYD/XhRPsLxR20RM64LPw+Y9Sdv6NaBkgXksx4+Qk92TzSR4O2lb0
ZFKbX6mUAHzB7qC/NOISAl0MWD30kkJybgtn6TrayUXnbT7gHgzFjf0+0URBF7B7
0QIDAQAB
-----END PUBLIC KEY-----";
    private $dokuPublicKey = "-----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwQY+iVi/dwZjm/2fXD+xmopuldumL8aBWPvkhqdy2Lcz7Fd2bwoLoW7xGRYzDLs2jC+CDwILynrnZlZQ+LBg8mzflqWPRiiHVf0VJrHmYGeS9rOY/R7V/dODE8QlxmkHZ52KGbZdphXKcCCP178+AdQyo7+8UXJUvlJ9VkYyWREDQ5q5XB/9cXxeD1MgwXcgZIVZ+2ZluHzX9bl6B7bJ8n8OXkUYsExfl3ixtz895tTq5P0eXSVVwg6Yb9LXXS7lBHkgmETgKtT4N2Cy3C1U2cfyRcNUtMXx7L84ecAVQMxfy122L9VE3I8eAckYY7vgvS1LpMyg4tVaDX108TdfVwIDAQAB
    -----END PUBLIC KEY-----";
    private $secretKey = 'SK-VknOxwR4xZSEPnG7fpJo';
    private $isProduction = "false";
    private $additionalInfo;
    private $cardData;

    protected function setUp(): void
    {
        $this->mockTokenController = m::mock(TokenController::class);
        $this->mockDirectDebitController = m::mock(DirectDebitController::class);

        $this->snap = new Snap($this->privateKey, $this->publicKey, $this->dokuPublicKey, $this->clientId, $this->issuer, $this->isProduction, $this->secretKey);
        $this->additionalInfo = new CardRegistrationAdditionalInfoRequestDto(
            'DIRECT_DEBIT_CIMB_SNAP',
            'John Doe',
            'john.doe@example.com',
            '1234567890',
            'Indonesia',
            'Jl. Merdeka No. 1',
            '19900101',
            'https://example.com/success',
            'https://example.com/failed'
        );
        $this->cardData = new CardRegistrationCardDataRequestDto("7820123123","D","0525","6013010111348228","02","email@email.com");
    }

    protected function tearDown(): void
    {
        m::close();
    }

    // Tests for Account Binding - Validate Request
    public function testShouldValidateSuccessfullyWithValidData()
    {
        
        $validData = new CardRegistrationRequestDto($this->cardData,"cust-001","081234567890",$this->additionalInfo);
        $this->assertNull($validData->validate());
    }

    public function testShouldThrowErrorWhenCustIdMerchantIsMissing()
    {
        $this->expectExceptionMessage("custIdMerchant cannot be null. Please provide custIdMerchant. Example: 'cust-001'.");
        $validData = new CardRegistrationRequestDto($this->cardData,"","081234567890",$this->additionalInfo);

        $validData->validate();
    }

    public function testShouldThrowErrorWhenSuccessRegistrationUrlIsMissing()
    {
        $this->expectExceptionMessage("additionalInfo.successRegistrationUrl cannot be null. Please provide a additionalInfo.successRegistrationUrl. Example: 'https://www.doku.com'.");
        $this->additionalInfo->successRegistrationUrl = '';
        $validData = new CardRegistrationRequestDto($this->cardData,"1242343423","081234567890",$this->additionalInfo);

        $validData->validate();
    }

    public function testShouldThrowErrorWhenFailedRegistrationUrlIsMissing()
    {
        $this->expectExceptionMessage("additionalInfo.failedRegistrationUrl cannot be null. Please provide a additionalInfo.failedRegistrationUrl. Example: 'https://www.doku.com'.");
        $this->additionalInfo->failedRegistrationUrl = '';
        $validData = new CardRegistrationRequestDto($this->cardData,"1242343423","081234567890",$this->additionalInfo);

        $validData->validate();
    }

    public function testShouldThrowErrorWhenChannelIsMissing()
    {
        $this->expectExceptionMessage("additionalInfo.channel cannot be null. Ensure that additionalInfo.channel is one of the valid channels. Example: 'DIRECT_DEBIT_ALLO_SNAP'.");
        $this->additionalInfo->channel = '';
        $validData = new CardRegistrationRequestDto($this->cardData,"1242343423","081234567890",$this->additionalInfo);

        $validData->validate();
    }

  
}

