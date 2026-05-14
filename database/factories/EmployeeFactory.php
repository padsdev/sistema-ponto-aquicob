<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * CPFs válidos (dígitos verificadores corretos) para uso em testes e seed.
     *
     * @var list<string>
     */
    private const VALID_CPF_POOL = [
        '72892158770', '98927267907', '73003562908', '50939519607', '61922737151',
        '42982168103', '01911204050', '03796877710', '58707488017', '38580134048',
        '63579070738', '87422532920', '09747934850', '73914511427', '12920782789',
        '31155860780', '97180238287', '11204812527', '04431872906', '51197850503',
        '29505627360', '86409354240', '32053297247', '08202382017', '29740880150',
        '46572718220', '88306545311', '36222050247', '86553812179', '49018951412',
        '47882954152', '09366589839', '43760893970', '74897707773', '67068335683',
        '32570253510', '90212142887', '93736398468', '35520786810', '61517645280',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => sprintf(
                '%s %s %s',
                fake()->unique()->firstName(),
                fake()->unique()->lastName(),
                fake()->unique()->lexify('????')
            ),
            'cpf' => fake()->unique()->randomElement(self::VALID_CPF_POOL),
            'position' => fake()->jobTitle(),
        ];
    }
}
