// Função para mostrar seções do estatuto
function showEstatutoSection(section) {
    const estatutoContent = document.getElementById('estatuto-content');
    const resumoEstatuto = document.getElementById('resumo-estatuto');
    const navButtons = document.querySelectorAll('.estatuto-nav-btn');
    
    // Resetar estilos dos botões
    navButtons.forEach(btn => {
        if (btn.textContent.toLowerCase().includes('capítulo') || btn.textContent.toLowerCase() === 'resumo') {
            btn.classList.remove('bg-[#8B2635]', 'text-white');
            btn.classList.add('bg-white', 'text-[#8B2635]', 'border', 'border-[#8B2635]');
        }
    });
    
    // Atualizar botão ativo
    if (section === 'resumo') {
        resumoEstatuto.style.display = 'block';
        estatutoContent.style.display = 'none';
        navButtons[0].classList.remove('bg-white', 'text-[#8B2635]', 'border');
        navButtons[0].classList.add('bg-[#8B2635]', 'text-white');
        return;
    }
    
    // Esconder resumo e mostrar conteúdo do estatuto
    resumoEstatuto.style.display = 'none';
    estatutoContent.style.display = 'block';
    
    // Conteúdo dos capítulos
    const capitulos = {
        'capitulo1': {
            titulo: 'Capítulo I - Da constituição, sede e foro',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo I - Da constituição, sede e foro</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 1º</h5>
                            <p class="text-[#8B2635]">O Centro do Comércio de Café do Rio de Janeiro - CCC RJ, associação civil, sem fins lucrativos, com personalidade jurídica distinta da de seus sócios, reúne, no quadro social, pessoas físicas e jurídicas ligadas, diretamente ou indiretamente, às atividades do comércio, produção, industrialização e prestação de serviços na área do café, ou interessadas em tais atividades, admitidas de acordo com o Estatuto.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 2º</h5>
                            <p class="text-[#8B2635]">O C.C.C.RJ., fundado em 19 de dezembro de 1901, é regido pelo presente Estatuto, com sede e foro na cidade do Rio de Janeiro (RJ) à Rua da Quitanda nº 191 - 8° e 10° andares - Centro - CEP n° 20.091-005, não tem prazo determinado de duração.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">← Voltar ao Resumo</button>
                    <button onclick="showEstatutoSection('capitulo2')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                </div>
            `
        },
        'capitulo2': {
            titulo: 'Capítulo II - Dos objetivos',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo II - Dos objetivos</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 3º</h5>
                            <p class="text-[#8B2635] mb-4">São objetivos do Centro:</p>
                            <ol class="list-decimal list-inside space-y-2 text-[#8B2635]">
                                <li>Promover estreita união dos que exercem sua atividade no comércio e nas demais áreas do café;</li>
                                <li>Defender os legítimos interesses de seus sócios, individualmente ou em grupo, administrativa ou judicialmente, em questões de natureza cafeeira;</li>
                                <li>Promover iniciativas voltadas para o desenvolvimento dos negócios do café e para o fortalecimento das estruturas operacionais no Estado do Rio de Janeiro, inclusive adotando medidas de estímulo ao incremento das exportações de café pelos portos estaduais e de apoio ao aumento da produção e do consumo regionais;</li>
                                <li>Preservar o acervo de informações e documentos sobre a história do café e do Centro, organizando biblioteca e local para a guarda e exposição pública de objetos de interesse histórico da cultura cafeeira, ou firmando convênios de cooperação em intercâmbios com Museus voltados para a economia cafeeira;</li>
                                <li>Desenvolver medidas de difusão de conhecimentos e informações sobre a história do café no Brasil e no Estado do Rio de Janeiro.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo1')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
                    <div class="space-x-4">
                        <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                        <button onclick="showEstatutoSection('capitulo3')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                    </div>
                </div>
            `
        },
        'capitulo3': {
            titulo: 'Capítulo III - Dos Sócios',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo III - Dos Sócios</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 5º</h5>
                            <p class="text-[#8B2635]">Os Sócios do Centro não respondem solidária ou subsidiariamente pelas obrigações sociais.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 6º</h5>
                            <p class="text-[#8B2635] mb-2">Os sócios do Centro, pessoas físicas ou jurídicas, devem participar das atividades mencionadas no Art. 1º e dividem-se em três categorias:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li><strong>Sócios quinhoístas ou efetivos</strong> - pessoas físicas ou jurídicas que, atendidas as condições previstas neste Estatuto, adquiram, no mínimo, 1 (um) quinhão de capital social, no valor de R$ 1.000,00 (mil reais) cada, e que poderão ser adquiridos à vista ou em até 10 (dez) parcelas mensais e sucessivas, sem juros, sendo a primeira no ato da assinatura do contrato de adesão e as demais com vencimento no mesmo dia dos meses subsequentes.</li>
                                <li><strong>Sócios Contribuintes</strong> - pessoas físicas ou jurídicas que, sem adquirir quinhão de capital social, paguem anualmente contribuição fixada em Assembléia Geral.</li>
                                <li><strong>Sócios Honorários</strong> - pessoas físicas ou jurídicas que, por relevantes serviços prestados ao Centro ou ao comércio de café, sejam agraciados com o título de sócio honorário, em Assembléia Geral, por indicação do Conselho Administrativo.</li>
                            </ol>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único: Nenhuma pessoa ou empresa tem ingresso no quadro social do Centro sem estar inscrita em uma das categorias de sócios indicadas neste artigo.</p>
                        </div>
                        <!-- Outros artigos do Capítulo III -->
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 33</h5>
                            <p class="text-[#8B2635]">As penalidades previstas para os sócios de qualquer categoria que infrinjam o Estatuto só serão aplicadas depois de assegurada ampla defesa ao infrator.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo2')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
                    <div class="space-x-4">
                        <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                        <button onclick="showEstatutoSection('capitulo4')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                    </div>
                </div>
            `
        },
        'capitulo4': {
    titulo: 'Capítulo IV - Da Assembléia Geral',
    conteudo: `
        <div class="mb-8">
            <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo IV - Da Assembléia Geral</h4>
            <div class="space-y-4">
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 34</h5>
                    <p class="text-[#8B2635]">A Assembléia Geral, órgão principal e soberano da administração do Centro, é a reunião dos sócios quinhoístas realizada em sua sede, observadas as disposições deste Estatuto. A Assembleia Geral pode ser ordinária ou extraordinária.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 35</h5>
                    <p class="text-[#8B2635]">Só podem tomar parte nos trabalhos da Assembléia e votar os sócios quinhoístas em pleno gozo de seus direitos e quites das contribuições que lhe competem.</p>
                    <div class="mt-2 space-y-2 text-[#8B2635]">
                        <p>§ 1º O número de sócios presentes e a apuração do quorum se verificam com base no número de quinhões de propriedade de cada um, comparado com o total de quinhões em poder dos sócios quinhoístas, em situação regular, observando-se idêntico critério nas votações, esclarecido que a cada quinhão corresponderá a um voto;</p>
                        <p>§ 2º Ao início dos trabalhos o Presidente da Assembléia informará o número total de quinhões em pleno gozo dos direitos sociais e o quorum necessário para as deliberações;</p>
                        <p>§ 3º A quitação das contribuições em atraso poderá ser feita na secretaria do Centro até 60 minutos antes do início da Assembléia; e,</p>
                        <p>§ 4º As pessoas físicas podem fazer-se representar por procurador, e as jurídicas por Diretor, Gerente ou Procurador. O mandato terá prazo determinado, não superior a 1 (um) ano, contado a partir da sua data de assinatura, podendo ser outorgado por instrumento público ou particular, carta, fax ou e-mail.</p>
                    </div>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 36</h5>
                    <p class="text-[#8B2635]">Para que se realize a Assembléia Geral, em primeira convocação, é necessária a presença de sócios quinhoístas detentores de pelo menos 2/3 (dois terços) dos quinhões em poder dos sócios quinhoístas; em segunda convocação, poderá ela reunir-se com qualquer número de sócios quinhoístas presentes. A segunda convocação pode realizar-se no mesmo dia, com espaço de 30 (trinta) minutos da primeira para a segunda, sempre participando sócios quinhoístas em pleno gozo de seus direitos e quites com as suas obrigações.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 37</h5>
                    <p class="text-[#8B2635]">Para que a Assembléia convocada para tratar da dissolução da sociedade, da forma de liquidação, cisão, alienação total ou parcial de imóveis ou outros bens do Centro e destinação do seu resultado, ou, ainda, constituição de qualquer ônus real, delibere validamente, é necessário o comparecimento de sócios quinhoístas em pleno gozo de seus direitos e quites das respectivas contribuições, detentores de pelo menos 3/4 (três quartos) do total dos quinhões em poder dos sócios quinhoístas, em situação regular.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 38</h5>
                    <p class="text-[#8B2635]">A Assembléia Geral é convocada pelo Presidente ou seu substituto, por meio de edital publicado uma vez em jornal de circulação no Rio de Janeiro e três vezes no Boletim do Café, editado pelo Centro do Comércio de Café ou, se este não existir, através de convite simples, com a antecedência mínima de 5 (cinco) dias.</p>
                    <p class="text-[#8B2635] mt-2">Parágrafo Único - Excetuadas as hipóteses previstas nos Art. 37, 45, 47, 48 e 49, a convocação poderá ser apenas pelo Boletim do Café ou carta-convite simples.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 39</h5>
                    <p class="text-[#8B2635]">Os editais e avisos indicarão sempre o local e horário da primeira e segunda convocação, mencionando o número de sócios quinhoístas necessários à realização da Assembléia.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 40</h5>
                    <p class="text-[#8B2635]">A Assembléia só trata dos assuntos constantes dos Editais de convocação e suas deliberações são, à exceção dos casos previstos neste Estatuto, por maioria de votos, sendo que a cada quinhão corresponderá a um voto.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 41</h5>
                    <p class="text-[#8B2635]">As Assembléias Gerais Extraordinárias realizam-se quando o julguem necessário o Conselho Administrativo, a Diretoria ou seu Presidente, ou, ainda, sócios quinhoístas possuidores de, no mínimo, dez quinhões.</p>
                    <p class="text-[#8B2635] mt-2">§ 1º - A resolução do Conselho Administrativo, ou da Diretoria, ou o requerimento dos sócios quinhoístas sobre convocação da Assembléia Geral Extraordinária devem estabelecer, clara e expressamente, o assunto que lhe vai ser submetido para que se mencione na convocação.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 42</h5>
                    <p class="text-[#8B2635]">Caso o Presidente não faça a convocação da Assembléia dentro do prazo de cinco dias contados da resolução do Conselho Administrativo ou da Diretoria, ou de entrega do requerimento assinado por sócios efetivos detentores de, pelo menos, dez quinhões, a convocação pode ser feita pelos que a tenham resolvido ou requerido, observado o Estatuto sobre prazos e publicações.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 43</h5>
                    <p class="text-[#8B2635]">Os trabalhos da Assembleia são dirigidos pelo Presidente, exceto quando deva tratar de assunto pertinente ao interesse ou responsabilidade da Diretoria ou de algum de seus membros. Na ausência do Presidente, o Diretor Secretário o substitui, e na ausência de ambos, o Diretor Tesoureiro ou Diretor do Patrimônio os substitui na direção dos trabalhos da Assembléia. Na ausência dos quatro membros da Diretoria, ou quando se deva tratar de assunto que se prenda a interesse ou responsabilidade dela ou de qualquer de seus membros, os trabalhos da Assembléia serão dirigidos por sócio efetivo quinhoísta eleito pelos presentes.</p>
                    <p class="text-[#8B2635] mt-2">Parágrafo Único - As votações de qualquer matéria da pauta serão por aclamação, exceto quando a presidência ou a própria Assembleia decidir que seja pelo voto secreto.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 44</h5>
                    <p class="text-[#8B2635]">Dos indicados para completar a mesa, o Presidente da Assembléia designa um Secretário com o encargo de lavrar a ata a ser assinada pelos componentes da mesa. Somente em casos especiais, por decisão da Assembléia, será a ata assinada pelos presentes.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 45</h5>
                    <p class="text-[#8B2635]">As Assembleias Gerais Ordinárias são convocadas para o primeiro quadrimestre de cada ano, pelo Presidente ou por seu substituto, para apreciação do relatório da Diretoria e parecer do Conselho Administrativo sobre as contas de receitas e despesas relativas ao ano civil findo em 31 de dezembro do ano imediatamente anterior.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 46</h5>
                    <p class="text-[#8B2635]">O relatório da administração e o balanço geral devem estar à disposição dos sócios quinhoístas, até três dias antes da data da Assembléia Geral Ordinária.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 47</h5>
                    <p class="text-[#8B2635]">Bienalmente, no mês de março, a Assembleia Geral Ordinária elege os membros do Conselho Administrativo e seus Suplentes, nela votando apenas os sócios quinhoístas, como previsto no Artigo 35 e parágrafo primeiro, devendo a eleição processar-se com observância das seguintes normas:</p>
                    <div class="mt-2 space-y-2 text-[#8B2635]">
                        <p>I - A eleição será feita por escrutínio secreto ou aclamação;</p>
                        <p>II - A eleição para o Conselho Administrativo, quando por escrutínio secreto, será feita mediante emprego de envelope fornecido pela mesa, no qual deverá ser colocada a chapa com os nomes dos 9 (nove) membros efetivos e 3 (três) suplentes;</p>
                        <p>III - Os componentes da mesa serão os escrutinadores e a apuração será feita logo após a votação;</p>
                        <p>IV - Aberta a urna para ter início a apuração, ninguém mais poderá votar, sob pena de nulidade do pleito;</p>
                        <p>V - Apurado o resultado da votação, serão imediatamente proclamados os eleitos para o Conselho Administrativo, pelo Presidente da Assembleia; e</p>
                        <p>VI - Ocorrendo empate na votação, será considerado eleito o sócio mais antigo no quadro de sócios quinhoístas; se persistir o empate, será considerado eleito o mais idoso.</p>
                    </div>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 48</h5>
                    <p class="text-[#8B2635]">É da competência da Assembléia Geral decidir quanto à concessão do título de SÓCIO HONORÁRIO, considerando-se aprovada a proposta ou indicação que, em votação nominal, alcançar sufrágios de sócios quinhoístas detentores de, no mínimo, 2/3 (dois terços) dos quinhões da sociedade.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 49</h5>
                    <p class="text-[#8B2635]">A Assembléia Geral é o órgão competente para cancelar título de SÓCIO HONORÁRIO, desde que as razões invocadas para isso estejam estribadas em atos comprovados por testemunhas ou documentos de que o respectivo titular haja, em público ou em particular, dentro ou fora do recinto social, se manifestado contra o Centro, Diretores ou Conselheiros no exercício de suas funções, causas em que o Centro esteja interessado, ou contra o comércio de café, ou ainda, praticado atos nocivos dentro da sede.</p>
                    <p class="text-[#8B2635] mt-2">Parágrafo Único - Para que a Assembléia Geral possa aplicar a medida de que trata o "caput" deste artigo, é necessário que sócios quinhoístas detentores de, no mínimo, 2/3 (dois terços) dos quinhões da sociedade, em votação nominal, assim deliberem.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 50</h5>
                    <p class="text-[#8B2635]">A Assembléia em que compareçam e votem favoravelmente sócios quinhoístas possuidores de 2/3 (dois terços) do total dos quinhões representativos do patrimônio do Centro, em poder dos sócios quinhoístas, pode destituir, individualmente ou coletivamente, os membros da Diretoria e do Conselho Administrativo, quando considerar essa medida necessária aos altos interesses do Centro.</p>
                    <div class="mt-2 space-y-2 text-[#8B2635]">
                        <p>§ 1º Caso seja mantido qualquer dos Diretores ou Conselheiros, este, ou o mais idoso, se forem vários, responde, na qualidade de Presidente, pelo expediente do Centro e toma as medidas necessárias para normalizar a administração, de acordo com o Estatuto;</p>
                        <p>§ 2º Caso a Assembleia resolva destituir todo o Conselho Administrativo, ela própria elegerá, por maioria de votos e por aclamação, Diretoria Provisória para responder pelo expediente e tomar as medidas previstas no parágrafo anterior; e,</p>
                        <p>§ 3º No caso de destituição de um ou mais Diretores, o Conselho, dentro dos dez dias seguintes à destituição, elegerá os respectivos substitutos. Se a destituição abranger todo o Conselho ou ainda toda a Diretoria, por deliberação da própria Assembléia será convocada outra, com observância do disposto no Art. 37, para eleição de novo Conselho, e este, dentro de vinte e quatro horas de sua posse, elegerá a nova Diretoria.</p>
                    </div>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 51</h5>
                    <p class="text-[#8B2635]">O sócio só pode usar a palavra, durante a Assembléia, quando concedida pelo Presidente.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 52</h5>
                    <p class="text-[#8B2635]">Se, durante a realização da Assembléia, algum sócio quiser perturbar os trabalhos, com apartes impróprios, considerações estranhas ao assunto em debate, expressões descorteses, insultuosas ou inconvenientes, compete ao Presidente chamar-lhe a atenção, cassar-lhe a palavra ou fazê-lo retirar-se do recinto.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 53</h5>
                    <p class="text-[#8B2635]">Caso se forme tumulto ou se torne impossível manter a ordem nos trabalhos da sessão, o Presidente pode suspendê-la, temporariamente ou até nova convocação.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 54</h5>
                    <p class="text-[#8B2635]">É permitido à Assembléia resolver a continuação dos trabalhos por mais de um dia, fixando-se o local, data e hora certas. Enquanto perdure a continuação da Assembléia, é mantida a mesma mesa na direção dos trabalhos, até que a Assembléia seja dada por encerrada.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 55</h5>
                    <p class="text-[#8B2635]">Assuntos gerais de interesse da classe podem ser tratados em reuniões de sócios quinhoístas, convocados por iniciativa da Diretoria ou em função de requerimento assinado por sócios quinhoístas detentores de 10 quinhões, podendo a reunião realizar-se imediatamente ou dentro dos três dias subsequentes, mediante avisos pessoais.</p>
                    <p class="text-[#8B2635] mt-2">Parágrafo Único - As conclusões das reuniões se tornam resoluções da entidade, caso digam respeito à prerrogativa dos sócios quinhoístas.</p>
                </div>
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button onclick="showEstatutoSection('capitulo3')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
            <div class="space-x-4">
                <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                <button onclick="showEstatutoSection('capitulo5')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
            </div>
        </div>
    `
},

        'capitulo5': {
            titulo: 'Capítulo V - Da Administração',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo V - Da Administração</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 56</h5>
                            <p class="text-[#8B2635]">A Administração do Centro compete à Diretoria e ao Conselho Administrativo, com atribuições definidas neste Estatuto, sendo os cargos privativos dos sócios quinhoístas.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 57</h5>
                            <p class="text-[#8B2635]">O mandato dos Diretores e Conselheiros é gratuito e tem a duração de 2 (dois) anos, permitida a reeleição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 58</h5>
                            <p class="text-[#8B2635]">Os cargos que compõem a Diretoria são: Presidente, Diretor-Secretário, Diretor-Tesoureiro e Diretor do Patrimônio.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 59</h5>
                            <p class="text-[#8B2635]">O Conselho Administrativo é composto de 9 (nove) membros efetivos e 3 (três) suplentes. Em reunião realizada logo após à posse, o Conselho elege 4 (quatro) de seus membros efetivos para desempenharem os cargos da Diretoria, durante o biênio do mandato do próprio Conselho.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único - Em se verificando uma ou mais vagas no Conselho Administrativo, são convocados para substituição os suplentes, atendendo-se ao número de votos com que foram eleitos e, em igualdade de condições, à idade. Caso já tenham sido convocados os três suplentes, o Conselho Administrativo pode convocar para a substituição outros sócios quinhoístas, de preferência que já tenham feito parte de Conselhos anteriores.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 60</h5>
                            <p class="text-[#8B2635]">Os Diretores, em casos de impedimentos, que não devem durar mais de noventa dias, substituem-se, reciprocamente, o Presidente pelo Diretor-Secretário; o Diretor-Secretário pelo Diretor-Tesoureiro; o Diretor-Tesoureiro pelo Diretor do Patrimônio. Qualquer dos Diretores poderá ser substituído pelo Presidente; ao Diretor que se encontre no exercício da presidência é facultado convidar outro membro do Conselho para qualquer substituição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 61</h5>
                            <p class="text-[#8B2635]">As vagas que se verificarem na Diretoria durante o biênio serão preenchidas por outros sócios quinhoístas que façam parte do Conselho Administrativo, reunindo-se este para a eleição dentro de cinco dias após a ocorrência da vaga. O substituto serve pelo tempo necessário para completar o mandato do substituído. Esse mesmo critério é seguido na hipótese de impedimento de algum Diretor, caso tal impedimento deva durar mais de noventa dias.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único - Se a vacância ocorrer dentro dos noventa dias finais do mandato, não haverá necessidade da realização de novas eleições para preenchimento dos cargos vagos.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 62</h5>
                            <p class="text-[#8B2635]">A Diretoria poderá, ouvido o Conselho Administrativo, estruturar, como unidades de apoio e consulta, departamentos encarregados do estudo e acompanhamento de atividades previstas nos objetos sociais do Centro.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único - Os departamentos poderão ser dirigidos por sócios de qualquer categoria, escolhidos pelo Conselho, sendo gratuito o mandato.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 63</h5>
                            <p class="text-[#8B2635]">Os Diretores e Conselheiros devem assinar o livro de presença às reuniões do Conselho Administrativo, cabendo a abertura e encerramento do livro ao presidente do Centro.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 64</h5>
                            <p class="text-[#8B2635]">Considera-se ter renunciado ao cargo de membro do Conselho Administrativo aquele que, sem motivo justificado, deixar de comparecer a três reuniões regularmente convocadas, ou se recusar a exercer o cargo da Diretoria para o qual tenha sido eleito.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 65</h5>
                            <p class="text-[#8B2635]">O Conselho Administrativo deve fixar a orientação a ser seguida pelo Centro, tendo em vista os objetivos indicados no Art. 4º, cabendo a sua execução à Diretoria.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo4')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
                    <div class="space-x-4">
                        <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                        <button onclick="showEstatutoSection('capitulo6')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                    </div>
                </div>
            `
        },
        'capitulo6': {
            titulo: 'Capítulo VI - Do Conselho Administrativo',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo VI - Do Conselho Administrativo</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 66</h5>
                            <p class="text-[#8B2635]">O Conselho Administrativo é provido por eleição bienal, em Assembléia Geral Ordinária a realizar-se no mês de março, podendo para ele ser eleito qualquer sócio quinhoísta, pessoa física ou jurídica, sendo permitida a reeleição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 67</h5>
                            <p class="text-[#8B2635]">A posse do Conselho Administrativo ocorre logo após a sua eleição pela Assembleia Geral Ordinária.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 68</h5>
                            <p class="text-[#8B2635]">O biênio para o qual é eleito o Conselho é contado da data da sua eleição e posse.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 69</h5>
                            <p class="text-[#8B2635]">Considera-se como tendo renunciado o membro do Conselho que não tomar posse do cargo para o qual foi eleito dentro de 08 (oito) dias contados da eleição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 70</h5>
                            <p class="text-[#8B2635]">Ao Conselho Administrativo compete:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Empenhar-se, juntamente com a Diretoria, objetivando a prosperidade do Centro, de forma que as classes que neste se agrupam e dão vida ao comércio de café tenham uma associação que bem as represente e defenda seus legítimos interesses;</li>
                                <li>Fixar, podendo alterá-las em qualquer tempo, as contribuições sociais, em valor que assegure a normal manutenção da entidade;</li>
                                <li>Colaborar com a Diretoria no sentido de cumprir e fazer cumprir as disposições deste Estatuto, as deliberações das Assembléias Gerais, da Diretoria e aquelas do próprio Conselho Administrativo;</li>
                                <li>Examinar e emitir parecer sobre assuntos de interesse geral, especialmente os da lavoura, da indústria e do comércio de café submetidos à sua apreciação por algum de seus membros, pela Diretoria ou qualquer sócio;</li>
                                <li>Providenciar sobre a substituição temporária de seus membros e dos da Diretoria;</li>
                                <li>Resolver sobre a convocação da Assembléia Geral Extraordinária, nos casos previstos no Estatuto;</li>
                                <li>Examinar o balanço anual apresentado pela Diretoria, as contas e respectivos comprovantes, os livros, registros e todos os documentos do arquivo e escrituração do Centro, emitindo o parecer a ser anexado ao relatório anual da Diretoria;</li>
                                <li>Aprovar orçamento anual proposto pela Diretoria e suas eventuais alterações;</li>
                                <li>Aprovar a tabela de vencimento dos funcionários do Centro;</li>
                                <li>Examinar e decidir sobre recursos contra atos da Diretoria;</li>
                                <li>Propor à Assembleia Geral o valor e o prazo de liquidação dos quinhões para efeito de resgate pelo Centro, bem como as condições gerais e regras para a utilização desses quinhões visando a aquisição de bens do Ativo Circulante e Permanente, por parte dos associados e terceiros;</li>
                                <li>Autorizar a Diretoria a praticar todos os atos necessários para o cumprimento das decisões tomadas pela Assembleia Geral, seguindo as regras estabelecidas;</li>
                                <li>Aprovar o Plano de Assistência Social;</li>
                                <li>Aprovar o funcionamento de departamentos e define as respectivas áreas de atuação, por proposta da Diretoria; e</li>
                                <li>Instituir, por proposta da Diretoria, Convenção de Condomínio do prédio do CCCRJ, localizado na Rua da Quitanda, 191- Centro/RJ e aprovar o seu respectivo Regimento Interno.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 71</h5>
                            <p class="text-[#8B2635]">O Conselho Administrativo reúne-se sempre que o exija o assunto da convocação feita por qualquer de seus membros ou da Diretoria. O Presidente do Centro, ou o Diretor que o esteja substituindo, preside as reuniões do Conselho Administrativo.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 72</h5>
                            <p class="text-[#8B2635]">As resoluções do Conselho só têm valor e produzem efeito quando constem do livro próprio, em ata lavrada pelo secretário e subscrita, pelo menos, pela maioria dos Conselheiros presentes à reunião.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 73</h5>
                            <p class="text-[#8B2635]">É permitido aos Conselheiros apresentarem seus votos por escrito para que constem na ata.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 74</h5>
                            <p class="text-[#8B2635]">As reuniões do Conselho Administrativo devem comparecer, pelo menos, 05 (cinco) de seus membros, sendo as deliberações tomadas por maioria de votos dos presentes e cabendo ao Presidente o voto de qualidade, no caso de empate.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único - Nas reuniões que o Conselho Administrativo realize no exercício das atribuições designadas nos incisos VII, VIII, IX e X do Art. 70 será escolhido para presidi-la um conselheiro que não faça parte da Diretoria, cabendo ao Presidente da reunião, também neste caso, o voto de qualidade.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 75</h5>
                            <p class="text-[#8B2635]">Quando, em suas reuniões, o Conselho haja de tratar de assunto relacionado com recursos contra atos da Diretoria, exame de contas ou de matéria que envolva a responsabilidade da Diretoria, os Diretores não têm direito a voto.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 76</h5>
                            <p class="text-[#8B2635]">As decisões aprovadas por maioria dos membros do Conselho tornam-se resoluções do Centro.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo5')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
                    <div class="space-x-4">
                        <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                        <button onclick="showEstatutoSection('capitulo7')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                    </div>
                </div>
            `
        },
        'capitulo7': {
            titulo: 'Capítulo VII - Da Diretoria',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo VII - Da Diretoria</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 77</h5>
                            <p class="text-[#8B2635]">À Diretoria compete:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Empenhar-se, juntamente com o Conselho Administrativo, objetivando a prosperidade do Centro, de forma que as Classes que nele se agrupam e dão vida ao comércio e aos assuntos do café nesta praça tenham uma associação que dignamente as represente e lhes defenda os legítimos interesses;</li>
                                <li>Cumprir e fazer cumprir o Estatuto, as deliberações da Assembléia Geral, do Conselho Administrativo e da própria Diretoria;</li>
                                <li>Administrar a Sociedade e seu patrimônio do modo mais conveniente à sua prosperidade e fins, praticando os atos necessários, desde que não importem em alienação ou oneração de bens imóveis;</li>
                                <li>Decidir sobre admissão de sócios quinhoístas e contribuintes, dentro do prazo de 20 dias, não sendo obrigada a declarar as razões de eventual recusa, que comporta recurso para o Conselho Administrativo;</li>
                                <li>Propor a concessão de títulos de Sócio Honorário e de Presidente de Honra, bem como a sua cassação, ao Conselho Administrativo;</li>
                                <li>Convocar as Assembleias Gerais, Ordinárias e Extraordinárias, na forma deste Estatuto;</li>
                                <li>Elaborar o orçamento anual e submetê-lo à aprovação do Conselho Administrativo;</li>
                                <li>Apresentar à Assembleia Geral Ordinária, anualmente, relatório de suas atividades e das contas do exercício, com o parecer do Conselho Administrativo;</li>
                                <li>Contratar e demitir funcionários, fixando-lhes os vencimentos e atribuições;</li>
                                <li>Representar o Centro ativa e passivamente, em juízo ou fora dele, podendo constituir procuradores especiais para atos ou negócios determinados;</li>
                                <li>Celebrar contratos e convênios com entidades públicas ou privadas, nacionais ou estrangeiras, visando ao cumprimento dos objetivos do Centro;</li>
                                <li>Elaborar e aprovar o Regimento Interno do Centro, submetendo-o à aprovação do Conselho Administrativo;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto ou pelo Regimento Interno.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 78</h5>
                            <p class="text-[#8B2635]">A Diretoria será composta por um Presidente, um Diretor-Secretário, um Diretor-Tesoureiro e um Diretor do Patrimônio, todos eleitos pelo Conselho Administrativo dentre seus membros, para um mandato de 2 (dois) anos, permitida a reeleição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 79</h5>
                            <p class="text-[#8B2635]">Compete ao Presidente:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Representar o Centro em juízo e fora dele, ativa e passivamente;</li>
                                <li>Convocar e presidir as reuniões da Diretoria e do Conselho Administrativo;</li>
                                <li>Executar as deliberações da Assembleia Geral, do Conselho Administrativo e da Diretoria;</li>
                                <li>Assinar, juntamente com o Diretor-Tesoureiro, os cheques, ordens de pagamento e demais documentos bancários;</li>
                                <li>Assinar, juntamente com o Diretor-Secretário, os atos, contratos e demais documentos do Centro;</li>
                                <li>Expedir instruções para a execução dos serviços do Centro;</li>
                                <li>Nomear comissões especiais para estudo de assuntos de interesse do Centro, podendo designar para integrá-las pessoas estranhas ao quadro social;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto ou pelo Regimento Interno.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 80</h5>
                            <p class="text-[#8B2635]">Compete ao Diretor-Secretário:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Substituir o Presidente em seus impedimentos eventuais ou temporários;</li>
                                <li>Secretariar as reuniões da Diretoria, do Conselho Administrativo e das Assembleias Gerais, lavrando as respectivas atas;</li>
                                <li>Assinar, juntamente com o Presidente, os atos, contratos e demais documentos do Centro;</li>
                                <li>Manter em dia a escrituração do Centro, inclusive o livro de presença dos membros da Diretoria e do Conselho Administrativo;</li>
                                <li>Expedir as comunicações e publicações oficiais do Centro;</li>
                                <li>Manter em ordem o arquivo da entidade;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto, pelo Regimento Interno ou pelo Presidente.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 81</h5>
                            <p class="text-[#8B2635]">Compete ao Diretor-Tesoureiro:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Substituir o Diretor-Secretário em seus impedimentos eventuais ou temporários;</li>
                                <li>Assinar, juntamente com o Presidente, os cheques, ordens de pagamento e demais documentos bancários;</li>
                                <li>Assinar, juntamente com o Diretor do Patrimônio, os documentos de movimentação de bens do Centro;</li>
                                <li>Apresentar à Diretoria, mensalmente, relatório da situação financeira do Centro;</li>
                                <li>Apresentar à Assembleia Geral Ordinária, anualmente, balanço e demonstrações contábeis do exercício, com o parecer do Conselho Fiscal, se houver;</li>
                                <li>Manter em dia a escrituração contábil do Centro;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto, pelo Regimento Interno ou pelo Presidente.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 82</h5>
                            <p class="text-[#8B2635]">Compete ao Diretor do Patrimônio:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Substituir o Diretor-Tesoureiro em seus impedimentos eventuais ou temporários;</li>
                                <li>Zelar pela conservação e manutenção do patrimônio do Centro;</li>
                                <li>Assinar, juntamente com o Diretor-Tesoureiro, os documentos de movimentação de bens do Centro;</li>
                                <li>Manter em dia o inventário dos bens móveis e imóveis do Centro;</li>
                                <li>Propor à Diretoria a alienação de bens móveis inservíveis ou obsoletos;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto, pelo Regimento Interno ou pelo Presidente.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 83</h5>
                            <p class="text-[#8B2635]">A Diretoria reunir-se-á, ordinariamente, uma vez por mês e, extraordinariamente, sempre que convocada pelo Presidente ou pela maioria de seus membros.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 84</h5>
                            <p class="text-[#8B2635]">As reuniões da Diretoria serão instaladas com a presença da maioria de seus membros e as deliberações serão tomadas por maioria de votos dos presentes, cabendo ao Presidente o voto de qualidade, em caso de empate.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 85</h5>
                            <p class="text-[#8B2635]">Os membros da Diretoria só poderão deliberar sobre matéria de seu interesse pessoal ou de seus parentes até o segundo grau, se presentes à reunião, devendo retirar-se da sala enquanto durar a discussão e a votação.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 86</h5>
                            <p class="text-[#8B2635]">As decisões da Diretoria serão registradas em livro próprio, assinadas pelos membros presentes à reunião.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo6')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
                    <div class="space-x-4">
                        <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                        <button onclick="showEstatutoSection('capitulo8')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
                    </div>
                </div>
            `
        },
        'capitulo8': {
    titulo: 'Capítulo VIII - Do Fundo Social',
    conteudo: `
        <div class="mb-8">
            <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo VIII - Do Fundo Social</h4>
            <div class="space-y-4">
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 84</h5>
                    <p class="text-[#8B2635]">O Fundo Patrimonial do Centro, englobando todos os ativos da sociedade, é de propriedade comum dos sócios quinhoístas, na proporção dos quinhões possuídos, se compõe:</p>
                    <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                        <li>Do edifício Sede, à Rua da Quitanda, 8º e 10º andares - Centro / Rio de Janeiro CEP n° 20.091-005, e de outros imóveis que venham a ser incorporados ao patrimônio da entidade;</li>
                        <li>Dos bens móveis que guarnecem sua sede;</li>
                        <li>Dos saldos em bancos ou estabelecimentos similares, e de outros valores mobiliários; e</li>
                        <li>De outros créditos e direitos assegurados em lei.</li>
                    </ol>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 85</h5>
                    <p class="text-[#8B2635]">Os bens imóveis do Centro podem ser alienados, cindidos, onerados ou gravados, no todo ou em parte, desde que com observância do que dispõe o presente Estatuto, em especial quanto ao disposto no Artigo 37, e no inciso XII do art. 70, sob pena de nulidade.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 86</h5>
                    <p class="text-[#8B2635]">Os bens móveis que guarnecem a sede do Centro serão conservados enquanto prestem bons serviços e atendam aos fins para que foram adquiridos, podendo a Diretoria determinar sua venda ou troca. O pagamento poderá ser feito também através de resgate de quinhões, observados os valores fixados pelo Conselho Administrativo nos termos do que dispõem os parágrafos 2º e 3º do art. 19 deste Estatuto.</p>
                </div>
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button onclick="showEstatutoSection('capitulo7')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
            <div class="space-x-4">
                <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                <button onclick="showEstatutoSection('capitulo9')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
            </div>
        </div>
    `
},
'capitulo9': {
    titulo: 'Capítulo IX - Da Receita e Despesa',
    conteudo: `
        <div class="mb-8">
            <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo IX - Da Receita e Despesa</h4>
            <div class="space-y-4">
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 87</h5>
                    <p class="text-[#8B2635]">Constituem RECEITAS do Centro:</p>
                    <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                        <li>As contribuições dos sócios quinhoístas e contribuintes;</li>
                        <li>Os rendimentos de seu patrimônio;</li>
                        <li>As doações, subvenções e auxílios que lhe forem concedidos;</li>
                        <li>As rendas eventuais; e</li>
                        <li>Outros rendimentos que, por qualquer título, lhe couberem.</li>
                    </ol>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 88</h5>
                    <p class="text-[#8B2635]">Constituem DESPESAS do Centro:</p>
                    <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                        <li>As despesas de manutenção e conservação de seu patrimônio;</li>
                        <li>As despesas com pessoal e encargos sociais;</li>
                        <li>As despesas administrativas e financeiras;</li>
                        <li>As despesas com eventos e campanhas; e</li>
                        <li>Outras despesas necessárias ao cumprimento de seus objetivos sociais.</li>
                    </ol>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 89</h5>
                    <p class="text-[#8B2635]">A Diretoria elaborará anualmente o orçamento de receita e despesa, que será submetido à aprovação do Conselho Administrativo.</p>
                </div>
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button onclick="showEstatutoSection('capitulo8')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
            <div class="space-x-4">
                <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
                <button onclick="showEstatutoSection('capitulo10')" class="bg-[#8B2635] text-white px-4 py-2 rounded-md hover:bg-[#6B1E2A] transition-colors">Próximo Capítulo →</button>
            </div>
        </div>
    `
},
'capitulo10': {
    titulo: 'Capítulo X - Disposições Gerais e Transitórias',
    conteudo: `
        <div class="mb-8">
            <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo X - Disposições Gerais e Transitórias</h4>
            <div class="space-y-4">
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 90</h5>
                    <p class="text-[#8B2635]">O exercício social coincidirá com o ano civil, encerrando-se em 31 de dezembro de cada ano.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 91</h5>
                    <p class="text-[#8B2635]">O presente Estatuto poderá ser reformado, no todo ou em parte, por deliberação da Assembleia Geral, especialmente convocada para este fim, com o comparecimento mínimo de 2/3 (dois terços) dos sócios quinhoístas em pleno gozo de seus direitos, e aprovação da maioria absoluta dos votos presentes.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 92</h5>
                    <p class="text-[#8B2635]">Os casos omissos neste Estatuto serão resolvidos pelo Conselho Administrativo, ad referendum da Assembleia Geral.</p>
                </div>
                <div class="bg-[#F5F0E8] p-4 rounded-lg">
                    <h5 class="font-bold text-[#6B2635] mb-2">Artigo 93</h5>
                    <p class="text-[#8B2635]">O presente Estatuto entrará em vigor na data de sua aprovação pela Assembleia Geral, revogadas as disposições em contrário, especialmente as contidas no Estatuto anterior.</p>
                </div>
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button onclick="showEstatutoSection('capitulo9')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
            <div class="space-x-4">
                <button onclick="showEstatutoSection('resumo')" class="text-[#8B2635] hover:underline">Voltar ao Resumo</button>
            </div>
        </div>
    `
}
    };
    

    // Atualizar conteúdo do capítulo selecionado
    if (capitulos[section]) {
        // Usar apenas o conteúdo do capítulo, sem adicionar o título novamente
        estatutoContent.innerHTML = capitulos[section].conteudo;
        
        // Atualizar botão ativo na navegação
        const buttonIndex = parseInt(section.replace('capitulo', ''));
        if (buttonIndex) {
            navButtons[buttonIndex].classList.remove('bg-white', 'text-[#8B2635]', 'border');
            navButtons[buttonIndex].classList.add('bg-[#8B2635]', 'text-white');
        }
    }
    
    // Rolagem suave para o topo do conteúdo
    estatutoContent.scrollIntoView({ behavior: 'smooth' });
}

// Inicializar a seção de estatutos
document.addEventListener('DOMContentLoaded', function() {
    // Garantir que o resumo esteja visível inicialmente
    const estatutoContent = document.getElementById('estatuto-content');
    if (estatutoContent) {
        estatutoContent.style.display = 'none';
    }
    
    // Adicionar evento de clique nos botões de navegação
    const navButtons = document.querySelectorAll('.estatuto-nav-btn');
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover classe ativa de todos os botões
            navButtons.forEach(btn => {
                btn.classList.remove('bg-[#8B2635]', 'text-white');
                btn.classList.add('bg-white', 'text-[#8B2635]', 'border', 'border-[#8B2635]');
            });
            // Adicionar classe ativa ao botão clicado
            this.classList.remove('bg-white', 'text-[#8B2635]', 'border');
            this.classList.add('bg-[#8B2635]', 'text-white');
        });
    });
});
