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
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 7º</h5>
                            <p class="text-[#8B2635]">Os sócios quinhoístas, também designados sócios efetivos, são coproprietários do patrimônio social e do acervo de bens do Centro, na proporção dos quinhões possuídos, nesta data em número de 167, devidamente registrados e numerados.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 8º</h5>
                            <p class="text-[#8B2635]">A admissão de sócio quinhoísta ou contribuinte dar-se-á mediante proposta de um sócio quinhoista, onde constará, além do nome, a nacionalidade, profissão, domicílio do proposto, e as razões que recomendam a aceitação do novo membro.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único: Para ser admitido como sócio quinhoísta deve o candidato adquirir um ou mais quinhões e preencher as demais condições exigidas por este Estatuto.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 9º</h5>
                            <p class="text-[#8B2635]">A transferência do quinhão adquirido para a titularidade do candidato proposto, somente se dará após a decisão favorável da Diretoria quanto à aceitação da proposta de admissão do sócio e depois de quitados eventuais débitos e contribuições do antigo titular do quinhão.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 10</h5>
                            <p class="text-[#8B2635]">Os quinhões, representativos do patrimônio social e do acervo de bens do Centro, se transferem livremente entre os sócios quinhoístas e destes para terceiros, exclusivamente para a admissão de novos sócios quinhoístas.</p>
                            <p class="text-[#8B2635] mt-2">§ 1º - É facultado ao sócio quinhoísta possuir mais de um quinhão do Centro;</p>
                            <p class="text-[#8B2635]">§ 2º - O sócio quinhoísta que possuir mais de um quinhão só poderá exercer os direitos previstos neste Estatuto se estiver em situação regular relativamente a todos os títulos possuídos; e,</p>
                            <p class="text-[#8B2635]">§ 3º - A transferência de quinhões deve ser necessariamente registrada em livro apropriado, na secretaria do Centro.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 11</h5>
                            <p class="text-[#8B2635]">É facultado ao Centro proceder ao resgate dos quinhões em poder dos sócios quinhoístas, nos casos previstos neste Estatuto, assim como receber 1 (um) ou mais quinhões por dação em pagamento por contribuições em atraso, ou para liquidação de outros débitos, e, ainda, por redução do patrimônio social.</p>
                            <p class="text-[#8B2635] mt-2">Parágrafo único: Poderá o Centro, observadas as condições aprovadas em Assembléia Geral e as regras deste Estatuto, alienar e/ou transferir bens integrantes de seus Ativos Circulante e Permanente aos seus associados, recebendo como pagamento quinhões de suas titularidades. O valor dos quinhões será aquele fixado pelo Conselho Administrativo, atendidas as condições gerais aprovadas em Assembléia Geral que tratar do assunto.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 12</h5>
                            <p class="text-[#8B2635]">No caso de sócio quinhoísta pessoa jurídica que venha a encerrar as suas atividades, e deixar de integrar o quadro social do Centro, o seu quinhão poderá ser transferido na forma do Artigo 10 ou resgatado, nos termos do Artigo 19.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 13</h5>
                            <p class="text-[#8B2635]">Falecendo o sócio quinhoísta pessoa física, seu quinhão se transmite aos sucessores, que ficarão investidos nos direitos e obrigações que competem aos sócios quinhoístas, desde que manifestem o seu interesse em se associar ao Centro.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 14</h5>
                            <p class="text-[#8B2635]">Em caso de sucessão comercial, o quinhão da firma sucedida pode ser transferido para a firma sucessora, preenchidas as condições exigidas neste Estatuto.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 15</h5>
                            <p class="text-[#8B2635] mb-2">São direitos do sócio quinhoísta:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Votar e ser votado;</li>
                                <li>Pedir a convocação de Assembleia Geral Extraordinária;</li>
                                <li>Tomar parte nas Assembléias Gerais, discutindo e apresentando propostas, com observância do edital de convocação;</li>
                                <li>Frequentar a sede social e suas dependências e gozar dos serviços destinados aos sócios;</li>
                                <li>Propor a admissão de sócios; e,</li>
                                <li>Transferir o quinhão de sua propriedade.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 16</h5>
                            <p class="text-[#8B2635] mb-2">São deveres do sócio quinhoísta:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Acatar as decisões das Assembléias Gerais, do Conselho Administrativo e da Diretoria;</li>
                                <li>Aceitar e desempenhar com dedicação os cargos para que seja designado;</li>
                                <li>Prestar ao Centro informações de interesse geral;</li>
                                <li>Cumprir e zelar pelo exato cumprimento do Estatuto; e,</li>
                                <li>Pagar, na época própria, as contribuições que lhe competem e fixadas pelo Conselho Administrativo.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 17</h5>
                            <p class="text-[#8B2635] mb-2">Suspende-se o exercício dos direitos de sócio quinhoísta:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Por comportamento inconveniente dentro do edifício da sociedade, depois da advertência verbal e repetida, por escrito, do Presidente;</li>
                                <li>Por procedimento irregular nos atos da vida comercial; e,</li>
                                <li>Pela falta de pagamento das contribuições que lhe competem.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 18</h5>
                            <p class="text-[#8B2635] mb-2">Perde-se a qualidade de sócio quinhoísta:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Pela cessão e transferência do total de quinhões de que seja possuidor;</li>
                                <li>Pela reincidência em procedimento ao qual já tenha sido imposta a pena de suspensão de que tratam os incisos I e II do artigo anterior; e,</li>
                                <li>Pela falta de pagamento de 06 (seis) contribuições fixadas pelo Conselho Administrativo. Neste caso os quinhões de sua titularidade poderão ser transferidos para outros sócios efetivos, ou para terceiros interessados em ingressar no quadro social, no prazo de 60 dias contados da perda da condição de sócio efetivo, ou ainda, resgatados pelo Centro na forma prevista nos incisos III e IV do Artigo 19, e de seus parágrafos 1º e 2º.</li>
                            </ol>
                            <p class="text-[#8B2635] mt-2">§ 1º - Em qualquer das hipóteses de transferência e resgate deverão ser quitadas as contribuições em atraso até a data da efetiva transferência; e,</p>
                            <p class="text-[#8B2635]">§ 2º - A readmissão do sócio, nesta hipótese fica subordinada ao preenchimento das condições estabelecidas no artigo 9º e seu parágrafo único, inclusive quanto à exigência de aquisição de novo quinhão.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 19</h5>
                            <p class="text-[#8B2635] mb-2">Sem prejuízo do direito do sócio quinhoísta de transferir livremente o quinhão de sua titularidade, o Centro se obriga a proceder ao resgate de quinhões nos seguintes casos e condições:</p>
                            <p class="text-[#8B2635] mt-2">§ 3º - Os quinhões resgatados serão objeto da correspondente baixa na quantidade de quinhões que formam o patrimônio social, que estará sempre representado pelo saldo remanescente em poder dos sócios quinhoístas.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 20</h5>
                            <p class="text-[#8B2635]">A pena de suspensão do exercício dos direitos de sócio quinhoísta é aplicada pela Diretoria, cabendo recurso ao Conselho Administrativo, no prazo de 10 dias e com efeito suspensivo. A pena de perda de qualidade de sócio quinhoísta é aplicada pelo Conselho Administrativo, por proposta da Diretoria ou de qualquer Conselheiro, cabendo recurso no prazo de 10 dias, com efeito suspensivo, para a primeira Assembléia Geral, ordinária ou extraordinária, a ser realizada no prazo máximo de 30 dias contados da data da pena aplicada pelo Conselho.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 21</h5>
                            <p class="text-[#8B2635]">Os sócios contribuintes, cujo número é ilimitado, têm, na vida da sociedade, a restrita atuação que lhes concede este Estatuto, sem direito, interesse, participação ou comunhão no patrimônio social, não podendo tomar parte nas Assembléias Gerais, nem votar ou ser votados.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 22</h5>
                            <p class="text-[#8B2635]">O sócio contribuinte pode frequentar a sede e suas dependências e gozar dos serviços destinados aos sócios.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 23</h5>
                            <p class="text-[#8B2635] mb-2">São deveres do sócio contribuinte:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Acatar as decisões das Assembléias Gerais, do Conselho Administrativo e da Diretoria;</li>
                                <li>Colaborar para o desenvolvimento do Centro;</li>
                                <li>Prestar à sociedade informações de interesse geral;</li>
                                <li>Cumprir e zelar pelo exato cumprimento do presente Estatuto; e</li>
                                <li>Pagar, nas épocas próprias, as contribuições a serem fixadas pelo Conselho Administrativo.</li>
                            </ol>
                            <p class="text-[#8B2635] mt-2">Parágrafo Único - As contribuições fixadas para os sócios contribuintes não podem ser inferiores às estipuladas para os sócios quinhoístas.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 24</h5>
                            <p class="text-[#8B2635] mb-2">Suspende-se o exercício dos direitos de sócio contribuinte:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Por decisão da Diretoria, sem necessidade de declinar as razões;</li>
                                <li>Por comportamento inconveniente no edifício da sociedade, depois de advertência verbal do Presidente, repetida por escrito;</li>
                                <li>Por procedimento irregular que torne inconveniente o seu convívio entre os sócios do Centro; e,</li>
                                <li>Pela falta de pagamento das contribuições que lhe competem.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 25</h5>
                            <p class="text-[#8B2635] mb-2">Perde-se a qualidade de sócio contribuinte:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Pela reincidência em procedimento a que já tenha sido imposta a pena de suspensão;</li>
                                <li>Por procedimento irregular nos atos da vida comercial; e</li>
                                <li>Pela falta de pagamento durante 06 meses das contribuições a que é obrigado.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 26</h5>
                            <p class="text-[#8B2635]">A readmissão de sócio contribuinte que tenha perdido tal condição, com fulcro no inciso III do artigo anterior, fica sujeita ao pagamento das contribuições em débito.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 27</h5>
                            <p class="text-[#8B2635]">Sócio Honorário é aquele que tenha prestado serviços relevantes à comunidade cafeeira, seja associado ou não, e a quem, por isso mesmo, a Assembléia Geral resolva conceder essa distinção, mediante proposta da Diretoria ou indicação firmada por sócios quinhoísta que sejam detentores de 10 quinhões, pelo menos.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 28</h5>
                            <p class="text-[#8B2635]">A proposta de concessão ou a indicação de que trata o artigo anterior só se considera aprovada se, em votação nominal, alcançar a seu favor o sufrágio de sócios quinhoístas detentores de 2/3 (dois terços) dos quinhões do Centro.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 29</h5>
                            <p class="text-[#8B2635]">O sócio quinhoísta, ao qual seja concedido o título de Sócio Honorário, continua com todos os direitos e obrigações que lhe são inerentes e sujeito às mesmas penalidades previstas para a categoria de sócios quinhoístas.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 30</h5>
                            <p class="text-[#8B2635]">Quando o sócio quinhoista, a quem foi concedido o título de Sócio Honorário, perde tal qualidade em função do disposto no inciso III do Art. 18, fica mantida a sua condição de Sócio Honorário.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 31</h5>
                            <p class="text-[#8B2635] mb-2">O título de Presidente de Honra é a mais alta honraria concedida pelo Centro e, por isso mesmo, excepcional, só podendo ser atribuído a quem, na condição de sócio quinhoísta, além de haver contribuído substancialmente para a projeção do Centro, tenha uma vida de trabalho, respeito e dedicação à causa do café e seja um exemplo de inspiração para as novas gerações.</p>
                            <p class="text-[#8B2635] mt-2">§ 1º - A concessão do título de Presidente de Honra pode ocorrer "post mortem";</p>
                            <p class="text-[#8B2635]">§ 2º - A outorga do título será acompanhada da entrega ao homenageado de uma medalha representativa, denominada "Medalha Marcellino Martins dos Santos Filho" e de um Diploma; e,</p>
                            <p class="text-[#8B2635]">§ 3º - O título de Presidente de Honra é mantido mesmo que o agraciado deixe de ser sócio quinhoísta da entidade.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 32</h5>
                            <p class="text-[#8B2635]">A concessão, pela Assembléia Geral, do título de Presidente de Honra, deve ser precedida de proposta da Diretoria, do Conselho Administrativo ou de sócios quinhoístas detentores de 10 quinhões, pelo menos, e receber a aprovação prevista no artigo 29.</p>
                        </div>
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
                            <p class="text-[#8B2635]">A Assembleia Geral Ordinária realizar-se-á anualmente, no mês de março, para apreciação das contas do exercício anterior, eleição e posse dos membros do Conselho Administrativo e Diretoria, quando for o caso, e demais assuntos de interesse social.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 36</h5>
                            <p class="text-[#8B2635]">A Assembleia Geral Extraordinária realizar-se-á sempre que necessário, por convocação do Presidente, do Conselho Administrativo ou de, no mínimo, 1/5 (um quinto) dos sócios quinhoístas em pleno gozo de seus direitos.</p>
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
            titulo: 'Capítulo V - Do Conselho Administrativo',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo V - Do Conselho Administrativo</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 62</h5>
                            <p class="text-[#8B2635]">O Conselho Administrativo é o órgão superior de orientação e fiscalização do Centro, composto por 15 (quinze) membros efetivos e 5 (cinco) suplentes, todos sócios quinhoístas no pleno gozo de seus direitos, eleitos pela Assembleia Geral para um mandato de 3 (três) anos, permitida a reeleição.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 63</h5>
                            <p class="text-[#8B2635]">Compete ao Conselho Administrativo:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Eleger, dentre seus membros, o Presidente, o Vice-Presidente e o Secretário do Conselho;</li>
                                <li>Fiscalizar os atos da Diretoria e opinar sobre as contas apresentadas pelo Diretor Tesoureiro;</li>
                                <li>Deliberar sobre a admissão e exclusão de sócios;</li>
                                <li>Conhecer dos recursos interpostos das decisões da Diretoria;</li>
                                <li>Elaborar o Regimento Interno do Centro;</li>
                                <li>Indicar os membros do Conselho Fiscal;</li>
                                <li>Indicar os membros da Diretoria Executiva, observado o disposto no Art. 76;</li>
                                <li>Deliberar sobre a aquisição e alienação de bens imóveis;</li>
                                <li>Autorizar a abertura de filiais ou agências do Centro;</li>
                                <li>Decidir sobre a representação do Centro em juízo ou fora dele, quando se tratar de interesse coletivo da categoria;</li>
                                <li>Decidir sobre a participação do Centro em outras sociedades ou entidades;</li>
                                <li>Deliberar sobre assuntos de interesse geral do Centro, não compreendidos na competência de outros órgãos.</li>
                            </ol>
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
            titulo: 'Capítulo VI - Da Diretoria',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo VI - Da Diretoria</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 77</h5>
                            <p class="text-[#8B2635] mb-2">À Diretoria compete:</p>
                            <ol class="list-decimal list-inside space-y-1 text-[#8B2635] ml-4">
                                <li>Executar as deliberações da Assembleia Geral e do Conselho Administrativo;</li>
                                <li>Administrar os bens e rendimentos do Centro, praticando todos os atos necessários à sua conservação e desenvolvimento;</li>
                                <li>Elaborar o orçamento anual e submetê-lo à aprovação do Conselho Administrativo;</li>
                                <li>Contratar e demitir funcionários, fixando-lhes os vencimentos e atribuições;</li>
                                <li>Representar o Centro ativa e passivamente, em juízo ou fora dele, podendo constituir procuradores especiais para atos ou negócios determinados;</li>
                                <li>Elaborar e apresentar ao Conselho Administrativo relatório anual das atividades desenvolvidas e das contas do exercício;</li>
                                <li>Convocar as Assembleias Gerais, ordinárias e extraordinárias;</li>
                                <li>Nomear as comissões especiais que julgar necessárias ao bom andamento dos trabalhos do Centro;</li>
                                <li>Exercer as demais atribuições que lhe forem conferidas pelo Estatuto ou pelo Regimento Interno.</li>
                            </ol>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 78</h5>
                            <p class="text-[#8B2635]">A Diretoria será composta por um Presidente, um Vice-Presidente, um Secretário, um Tesoureiro e um Diretor de Patrimônio, todos eleitos pelo Conselho Administrativo para um mandato de 3 (três) anos, permitida a recondução.</p>
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
            titulo: 'Capítulo VII - Disposições Gerais e Transitórias',
            conteudo: `
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-[#6B2635] mb-4">Capítulo VII - Disposições Gerais e Transitórias</h4>
                    <div class="space-y-4">
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 90</h5>
                            <p class="text-[#8B2635]">O exercício social coincidirá com o ano civil, encerrando-se em 31 de dezembro de cada ano.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 91</h5>
                            <p class="text-[#8B2635]">O Centro poderá ser dissolvido por deliberação da Assembleia Geral, especialmente convocada para este fim, com o comparecimento mínimo de 2/3 (dois terços) dos sócios quinhoístas em pleno gozo de seus direitos.</p>
                        </div>
                        <div class="bg-[#F5F0E8] p-4 rounded-lg">
                            <h5 class="font-bold text-[#6B2635] mb-2">Artigo 92</h5>
                            <p class="text-[#8B2635]">Para a reforma deste Estatuto é necessária a realização de Assembléia Geral Extraordinária, especialmente convocada para esse fim, com o comparecimento mínimo de sócios quinhoístas em pleno gozo de seus direitos, detentores de 3/4 (três quartos) dos quinhões representativos do patrimônio do Centro, em poder dos sócios quinhoístas.</p>
                            <p class="text-[#8B2635] mt-2">§ 1º O presente estatuto é reformável também no tocante à administração do Centro; e,</p>
                            <p class="text-[#8B2635]">§ 2º No caso de extinção do Centro, a Assembléia Geral dos quinhoístas deliberará sobre o destino do patrimônio.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button onclick="showEstatutoSection('capitulo6')" class="text-[#8B2635] hover:underline">← Capítulo Anterior</button>
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
