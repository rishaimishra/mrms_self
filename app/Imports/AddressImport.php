namespace App\Imports;

use App\Models\AdditionalAddress;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AddressImport implements ToCollection, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new FourthSheetImport()
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            dd($row); // Corrected from `dd(row)` to `dd($row)`
            AdditionalAddress::create([
                'title' => $row[0],
            ]);
        }
    }
}
