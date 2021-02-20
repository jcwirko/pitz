<?php

class YearCounter
{

    private $count = [];
    private $years = [];

    /*
     * SE CARGA EL ARRAY CON LOS DATOS A CALCULARA
     */
    public function __construct($years)
    {
        $this->years = $years;
    }

    /**
     * GENERA EL RANGO DE AÑOS
     */
    function ranges()
    {
        foreach ($this->years as $range) {
            $this->counter(range($range->birthYear, $range->deathYear));
        }

    }

    /*
     * CUENTA LAS VECES QUE APARECE CADA AÑO
     */
    function counter($range)
    {
        foreach ($range as $r) {
            array_key_exists($r, $this->count)
                ? $this->count[$r]++
                : $this->count[$r] = 1;
        }
    }

    /*
     * IMPRIME LOS RESULTADOS EN PANTALLA
     */
    function result()
    {
        echo '<p>Los años con más personas vivas fueron: </p>';

        echo '<pre>';
        foreach (array_keys($this->count, max($this->count)) as $d) {
            echo "$d <br>";
        }
        echo '</pre>';

        echo "<p>con un cantidad de: <strong>" .  max($this->count) . "</strong> personas </p>";
    }
}

$year = new YearCounter(json_decode(file_get_contents("data.json")));
$year->ranges();
$year->result();

//AL EJECUTAR EL CÓDIGO SE MOSTRARÁ

/*************************************

Los años con más personas vivas fueron:

1948
1949

con un cantidad de: 2586 personas

***************************************/
