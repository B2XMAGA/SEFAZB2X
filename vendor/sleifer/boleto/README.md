# Boleto
Classe para geração de código de barras (boleto e arrecadação) em php

# Modo de usar

use sleifer\boleto\Boleto;

$bar = '03399.58159 43600.000319 25146.301012 6 91140000044315'; <br>
$bar = '83630000020-2 45790006000-7 00101202243-8 54187880603-6'; <br>

```
$boleto = new Boleto($bar);
if(!$boleto->getError()){
    echo $boleto->getBarCode();
}else{
    echo $boleto->getError();
}
```

