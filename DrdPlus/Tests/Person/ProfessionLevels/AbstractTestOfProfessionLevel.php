<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Priest;
use DrdPlus\Professions\Ranger;
use DrdPlus\Professions\Theurgist;
use DrdPlus\Professions\Thief;
use DrdPlus\Professions\Wizard;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Profession;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

abstract class AbstractTestOfProfessionLevel extends TestWithMockery
{

    abstract public function I_can_create_it($professionCode);

    abstract public function I_can_create_it_with_default_level_up_at();

    abstract public function I_can_get_level_details($professionCode);

    /**
     * @param string $propertyCode
     * @param string $professionCode
     *
     * @return bool
     */
    protected function isPrimaryProperty($propertyCode, $professionCode)
    {
        return in_array($propertyCode, $this->getPrimaryProperties($professionCode), true);
    }

    /**
     * @param string $professionCode
     * @return string[]
     */
    protected function getPrimaryProperties($professionCode)
    {
        switch ($professionCode) {
            case ProfessionCodes::FIGHTER :
                return [Strength::STRENGTH, Agility::AGILITY];
            case ProfessionCodes::THIEF :
                return [Agility::AGILITY, Knack::KNACK];
            case ProfessionCodes::RANGER :
                return [Strength::STRENGTH, Knack::KNACK];
            case ProfessionCodes::WIZARD :
                return [Will::WILL, Intelligence::INTELLIGENCE];
            case ProfessionCodes::THEURGIST :
                return [Intelligence::INTELLIGENCE, Charisma::CHARISMA];
            case ProfessionCodes::PRIEST :
                return [Will::WILL, Charisma::CHARISMA];
        }
        throw new \LogicException;
    }

    /**
     * @param string $professionCode
     * @return MockInterface|Profession|Fighter|Wizard|Priest|Theurgist|Thief|Ranger
     */
    protected function createProfession($professionCode)
    {
        $profession = \Mockery::mock($this->getProfessionClass($professionCode));
        $profession->shouldReceive('isPrimaryProperty')
            ->andReturnUsing(
                function ($propertyCode) use ($professionCode) {
                    return in_array($propertyCode, $this->getPrimaryProperties($professionCode), true);
                }
            );
        $profession->shouldReceive('getPrimaryProperties')
            ->andReturn($this->getPrimaryProperties($professionCode));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);

        return $profession;
    }

    /**
     * @param string $professionCode
     * @return string|Profession
     */
    private function getProfessionClass($professionCode)
    {
        $reflection = new \ReflectionClass(Profession::class);
        $namespace = $reflection->getNamespaceName();

        return $namespace . '\\' . ucfirst($professionCode);
    }

}