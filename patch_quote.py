import re

with open("app/Services/QuoteGeneratorService.php", "r") as f:
    content = f.read()

replacement = """
                $priceInfo = $this->pricing[$id] ?? ['name' => $srv['name'], 'price' => 0, 'type' => 'custom', 'period' => '-'];
                
                if (isset($srv['final_price'])) {
                    $costoUnitario = (float) $srv['final_price'];
                    if (!isset($this->pricing[$id])) {
                        $priceInfo['period'] = 'annuale'; 
                    }
                } else {
                    $costoUnitario = $priceInfo['price'];
                }
                
                $costoTotale = $costoUnitario * $totalQty;
"""

content = re.sub(
    r"\$priceInfo = \$this->pricing\[\$id\] \?\? \['name' => \$srv\['name'\], 'price' => 0, 'type' => 'custom', 'period' => '-'\];\s*\$costoUnitario = \$priceInfo\['price'\];\s*\$costoTotale = \$costoUnitario \* \$totalQty;",
    replacement.strip(),
    content,
    flags=re.DOTALL
)

with open("app/Services/QuoteGeneratorService.php", "w") as f:
    f.write(content)
