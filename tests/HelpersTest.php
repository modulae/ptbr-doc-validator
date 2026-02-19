<?php

use function Modulae\PTBRDocValidator\cnpjToString;

it('can normalize a cnpj string', function () {
    expect(cnpjToString('T6.JSP.XPS/0001-11'))->toBe('T6JSPXPS000111');
});

it('can normalize an array of cnpj strings', function () {
    $cnpjs = ['T6.JSP.XPS/0001-11', '32.332.643/0001-29'];
    $expected = ['T6JSPXPS000111', '32332643000129'];

    expect(cnpjToString($cnpjs))->toBe($expected);
});

it('can normalize nested arrays of cnpj strings', function () {
    $cnpjs = ['T6.JSP.XPS/0001-11', ['32.332.643/0001-29']];
    $expected = ['T6JSPXPS000111', ['32332643000129']];

    expect(cnpjToString($cnpjs))->toBe($expected);
});
