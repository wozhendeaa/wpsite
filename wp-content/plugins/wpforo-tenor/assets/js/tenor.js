(() => {
    /* global wpfTenor */

    class wpfMasonry {
        constructor (grid, gridCell, gridCellWidth, gridGutter) {
            this.grid = grid
            this.gridCell = gridCell
            this.gridCellWidth = gridCellWidth
            this.gridGutter = gridGutter

            window.addEventListener('resize', this.fix.bind(this))
        }

        fix () {
            this.masonry(
                this.grid,
                this.gridCell,
                this.gridCellWidth,
                this.gridGutter
            )
        }

        /**
         * Calculate the masonry
         *
         * Calculate the average of heights of masonry-bricks and then
         * set it as the height of the masonry element.
         *
         * @param grid          Object  The Masonry Element
         * @param gridCell      Object  The Masonry bricks
         * @param gridCellWidth Integer Number Fixed Width of one cell
         * @param gridGutter    Integer The Vertical Space between bricks
         */
        masonry (grid, gridCell, gridCellWidth = 200, gridGutter = 8) {
            const g = document.querySelector(grid)
            const gc = document.querySelectorAll(gridCell)
            if (g && gc) {
                const gcLength = gc.length // Total number of cells in the masonry
                let gHeight = 0 // Initial height of our masonry
                let i // Loop counter

                // Calculate the net height of all the cells in the masonry
                for (i = 0; i < gcLength; ++i) {
                    gHeight += gc[i].offsetHeight + gridGutter
                }

                /*
                 * Calculate and set the masonry height based on the columns
                 * provided for big, medium, and small screen devices.
                 */
                const gWidth = g.clientWidth - 32
                g.style.height = Math.ceil(
                                 gHeight / Math.floor(gWidth / (gridCellWidth + gridGutter))
                                 + gHeight / (gcLength + 1)
                ) + 'px'
            }
        }
    }

    const wpfmasonry = new wpfMasonry(
        '#wpf-gifs',
        '#wpf-gifs > *',
        200, 8
    )

    class Tenor {
        constructor () {
            this.init()
            this.enableEvents().then(() => {})

            this.logs = new Map()
            this.dataset = new wpfGifsDataset(this)
            this.trendingSearches = []
        }

        init () {
            // this.title            = `<img src="${wpfTenor['WPFOROTENOR_URL']}/assets/ico/tenor.png" alt="Tenor Logo" width="12" height="12" style="margin-right: 5px">TENOR`;
            this.title = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="200px" height="50px" viewBox="0 0 1386 177" version="1.1">
                                    <!-- Generator: Sketch 58 (84663) - https://sketch.com -->
                                    <title>PB_tenor_logo_blue_horizontal</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs>
                                        <polygon id="path-1" points="9.33196144e-05 0.136430828 92.9189747 0.136430828 92.9189747 175.842699 9.33196144e-05 175.842699"/>
                                        <polygon id="path-3" points="0.00015818067 0.38312324 82.7284902 0.38312324 82.7284902 137.280001 0.00015818067 137.280001"/>
                                    </defs>
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="PB_tenor_logo_blue_horizontal" transform="translate(0.812500, 0.000000)">
                                            <g id="Group-13-Copy-13" transform="translate(759.187500, 0.000000)">
                                                <path d="M201.399227,97.2809141 C200.80605,92.1400423 199.342878,87.3946222 197.405165,82.7678377 C185.857976,55.6003076 155.843194,54.8884946 140.420579,68.1361257 C132.31382,75.0960752 127.528854,83.9937378 125.156144,94.2754814 C124.918873,95.2245654 124.839783,96.1736494 124.642057,97.2809141 L201.399227,97.2809141 Z M124.20706,117.409404 C125.156144,124.527534 127.212493,130.894306 130.850649,136.628356 C139.392405,150.113258 151.809587,156.519575 167.706745,156.400939 C180.75665,156.321849 191.947932,151.576429 201.596953,142.876492 C206.658734,138.289253 213.776865,139.159246 217.33593,144.656025 C219.906366,148.610541 219.392278,153.395507 215.833213,156.954572 C205.037383,167.750402 192.185203,174.473081 176.960314,176.331704 C166.994932,177.557604 157.069095,177.320333 147.380528,174.3149 C125.314325,167.473586 110.643068,152.841874 104.118115,130.854761 C96.485898,105.031767 100.55905,81.0278503 118.552101,60.3852729 C130.771558,46.4258288 146.589625,39.7822407 165.215399,40.098602 C181.270737,40.3754182 195.269726,45.8326513 206.540099,57.4984757 C215.912304,67.1870417 221.092721,79.0110467 223.465431,92.1400423 C224.374969,97.1227334 224.770421,102.184515 225.007692,107.246296 C225.284508,112.822165 220.93454,117.172133 215.358671,117.448949 C214.567768,117.488495 213.776865,117.448949 212.985961,117.448949 L127.014767,117.448949 C126.223864,117.409404 125.393415,117.409404 124.20706,117.409404 L124.20706,117.409404 Z" id="Fill-1" fill="#007ADD"/>
                                                <path d="M496.801628,109.382763 C496.603902,95.8187711 492.689326,84.5088532 484.107629,74.9389227 C466.589515,55.3640648 434.597079,56.115423 417.790383,76.2834584 C403.791394,93.0901545 402.802765,122.74903 419.055829,141.018897 C433.52936,157.311506 459.55008,160.672846 477.26671,148.018392 C490.751217,138.369371 496.406176,124.726288 496.801628,109.382763 M383.465025,107.56329 C383.504723,72.6057578 410.593163,39.7437237 452.036498,39.8226716 C490.672127,39.8623592 520.291457,69.7980509 520.212525,108.710496 C520.133276,146.950673 489.921164,176.767333 451.20605,176.807274 C413.914957,176.807274 383.386088,145.645682 383.465025,107.56329" id="Fill-3" fill="#007ADD"/>
                                                <path d="M270.172663,59.0416467 C272.664008,56.6689367 274.680812,54.6521332 276.776706,52.7539651 C286.900269,43.500396 299.00109,39.7040599 312.485992,39.8226954 C323.123246,39.9413309 333.128569,42.3140409 341.986687,48.483087 C352.979848,56.1548495 358.951564,67.0297706 361.284333,79.9610403 C362.036087,84.152828 362.431538,88.4632513 362.470688,92.7341294 C362.589719,116.935772 362.510629,141.097869 362.510629,165.299512 C362.510629,169.926296 359.584286,174.31581 355.273863,175.897616 C350.765714,177.558513 346.534381,176.886246 343.054406,173.406271 C340.602606,170.994016 339.534491,167.949038 339.534491,164.469063 C339.574432,142.046953 339.653522,119.664388 339.455796,97.2422785 C339.416251,92.8132197 338.783528,88.2655255 337.715809,83.9946474 C334.354469,70.905197 324.982265,62.9566183 311.576453,61.4143568 C302.046068,60.3070921 292.990224,61.8098085 285.041646,67.5834029 C274.483086,75.2551654 270.251753,86.1696316 270.212208,98.8240852 C270.133118,120.890289 270.212208,142.996037 270.172663,165.062241 C270.172663,173.524906 261.947268,179.021685 254.117325,175.818526 C249.965082,174.118084 247.157375,169.609935 247.157375,164.548153 L247.157375,129.55068 L247.157375,51.4489746 C247.157375,46.9803707 249.213724,43.5794863 253.01006,41.3649569 C256.925032,39.0713372 260.998184,39.1899727 264.834065,41.6022279 C268.116314,43.6981218 270.093572,46.7430997 270.172663,50.7767068 C270.212208,53.3866878 270.172663,55.9175785 270.172663,59.0416467" id="Fill-5" fill="#007ADD"/>
                                                <g id="Group-9" transform="translate(0.000000, 0.259021)">
                                                    <mask id="mask-2" fill="white">
                                                        <use xlink:href="#path-1"/>
                                                    </mask>
                                                    <g id="Clip-8"/>
                                                    <path d="M49.1109457,42.0541105 L51.6813816,42.0541105 L80.1539021,42.0541105 C84.4247802,42.2526273 87.9047549,43.6367081 90.1192843,47.4725894 C93.7969848,53.9184517 90.0401939,61.7088497 82.6847928,62.8161143 C81.5379829,62.974295 80.3120828,63.0138402 79.1257277,63.0130493 L51.6418364,63.0130493 L49.0362053,63.0130493 L49.0362053,65.228765 L49.0362053,135.816889 C49.1109457,140.562309 49.8623039,145.110003 53.0654624,148.985034 C55.6754435,152.148648 59.1949634,153.493579 63.0308446,154.086756 C68.7648939,154.996295 74.459398,154.521358 80.0352666,152.78137 C85.334319,151.080928 90.5542811,153.453638 92.3338136,158.396784 C94.1528913,163.41902 91.5824555,168.362166 86.6788547,170.734876 C80.0352666,173.977975 73.0753171,175.401206 65.719916,175.796658 C46.6591453,176.746137 29.5360878,162.905328 26.6492906,144.002738 C26.2538389,141.550938 26.1352034,139.019652 26.1352034,136.528306 C26.0956582,112.998932 26.0956582,89.4695572 26.1000082,65.9401826 L26.1000082,63.211566 C25.2256646,63.1720208 24.5138515,63.0929305 23.8415837,63.0929305 C19.4916153,63.0929305 15.1416469,63.1720208 10.7916785,63.0533853 C3.67354833,62.856055 -0.913691093,57.5174574 0.154028427,50.7943834 C0.905386608,46.0489634 5.21580985,42.4898983 10.6334978,42.3317176 C14.9043759,42.2130821 19.2147991,42.2925679 23.4856772,42.2925679 C26.0956582,42.2925679 26.0956582,42.2925679 26.1000082,39.5635559 L26.1000082,12.0796646 C26.0956582,5.3569861 30.9597138,0.21611434 37.4451213,0.136130456 C43.9305287,0.0579336702 49.0318553,5.2383506 49.0714005,12.0405148 C49.1109457,21.2541434 49.0714005,30.5077126 49.0714005,39.722132 C49.1109457,40.4730948 49.1109457,41.2639981 49.1109457,42.2526273 L49.1109457,42.0541105 Z" id="Fill-7" fill="#007ADD" mask="url(#mask-2)"/>
                                                </g>
                                                <g id="Group-12" transform="translate(542.955148, 39.408737)">
                                                    <mask id="mask-4" fill="white">
                                                        <use xlink:href="#path-3"/>
                                                    </mask>
                                                    <g id="Clip-11"/>
                                                    <path d="M23.0945359,28.1351212 C26.4954203,24.2596948 29.5004576,20.4238135 32.9808278,17.0229291 C40.9294064,9.27207631 50.2616705,3.85438838 61.2556225,1.67940417 C64.5770211,1.00713632 68.0569958,0.848955654 71.4582757,1.00713632 C77.904138,1.36304283 82.7286484,6.7411856 82.7286484,13.0684124 C82.7286484,19.3165488 77.8246522,24.6156013 71.4582757,25.2087788 C66.7520053,25.6437756 61.9674355,25.722866 57.3406509,26.7114951 C44.6466522,29.4401117 35.6698992,37.1118742 29.8959093,48.6195179 C24.8341279,58.6639904 23.1340811,69.4993663 23.0945359,80.6115583 C23.0154456,95.4409961 23.0945359,110.270434 23.0945359,125.099872 C23.0945359,131.980731 18.0323591,137.319328 11.5074064,137.280001 C4.94330411,137.240238 0.00015818067,132.138911 0.00015818067,125.258052 L0.00015818067,11.802967 C0.00015818067,6.66209526 3.63831358,1.99576551 8.54151889,0.769865319 C13.9200571,-0.574670373 19.1400192,1.639859 21.4727886,6.58300493 C22.3823275,8.48117296 22.8968101,10.6957023 22.975505,12.8311414 C23.2131714,17.220655 23.0545953,21.6497137 23.0545953,26.0787725 C23.0945359,26.7510403 23.0945359,27.383763 23.0945359,28.1351212" id="Fill-10" fill="#007ADD" mask="url(#mask-4)"/>
                                                </g>
                                            </g>
                                            <path d="M35.125,68.125 C41.5416988,68.125 47.2916413,69.5416525 52.375,72.375 C57.4583588,75.2083475 61.4374856,79.1666412 64.3125,84.25 C67.1875144,89.3333588 68.625,95.2083 68.625,101.875 C68.625,108.5417 67.1875144,114.437474 64.3125,119.5625 C61.4374856,124.687526 57.4583588,128.666653 52.375,131.5 C47.2916413,134.333347 41.5416988,135.75 35.125,135.75 C30.3749762,135.75 26.0208531,134.833343 22.0625,133 C18.1041469,131.166657 14.7500138,128.500018 12,125 L12,159.25 L0,159.25 L0,68.75 L11.5,68.75 L11.5,79.25 C14.16668,75.583315 17.5416462,72.8125094 21.625,70.9375 C25.7083538,69.0624906 30.2083088,68.125 35.125,68.125 Z M34.125,125.25 C38.3750212,125.25 42.1874831,124.270843 45.5625,122.3125 C48.9375169,120.354157 51.6041569,117.604184 53.5625,114.0625 C55.5208431,110.520816 56.5,106.458356 56.5,101.875 C56.5,97.2916437 55.5208431,93.2291844 53.5625,89.6875 C51.6041569,86.1458156 48.9375169,83.4166762 45.5625,81.5 C42.1874831,79.5833238 38.3750212,78.625 34.125,78.625 C29.9583125,78.625 26.1875169,79.6041569 22.8125,81.5625 C19.4374831,83.5208431 16.7708431,86.2499825 14.8125,89.75 C12.8541569,93.2500175 11.875,97.2916437 11.875,101.875 C11.875,106.458356 12.8333238,110.520816 14.75,114.0625 C16.6666762,117.604184 19.3333163,120.354157 22.75,122.3125 C26.1666837,124.270843 29.9583125,125.25 34.125,125.25 Z M112.625,135.75 C106.041634,135.75 100.125026,134.291681 94.875,131.375 C89.6249737,128.458319 85.5208481,124.437526 82.5625,119.3125 C79.6041519,114.187474 78.125,108.375033 78.125,101.875 C78.125,95.3749675 79.6041519,89.5625256 82.5625,84.4375 C85.5208481,79.3124744 89.6249737,75.3125144 94.875,72.4375 C100.125026,69.5624856 106.041634,68.125 112.625,68.125 C119.208366,68.125 125.104141,69.5624856 130.3125,72.4375 C135.520859,75.3125144 139.604152,79.3124744 142.5625,84.4375 C145.520848,89.5625256 147,95.3749675 147,101.875 C147,108.375033 145.520848,114.187474 142.5625,119.3125 C139.604152,124.437526 135.520859,128.458319 130.3125,131.375 C125.104141,134.291681 119.208366,135.75 112.625,135.75 Z M112.625,125.25 C116.875021,125.25 120.687483,124.270843 124.0625,122.3125 C127.437517,120.354157 130.083324,117.604184 132,114.0625 C133.916676,110.520816 134.875,106.458356 134.875,101.875 C134.875,97.2916437 133.916676,93.2291844 132,89.6875 C130.083324,86.1458156 127.437517,83.4166762 124.0625,81.5 C120.687483,79.5833238 116.875021,78.625 112.625,78.625 C108.374979,78.625 104.562517,79.5833238 101.1875,81.5 C97.8124831,83.4166762 95.1458431,86.1458156 93.1875,89.6875 C91.2291569,93.2291844 90.25,97.2916437 90.25,101.875 C90.25,106.458356 91.2291569,110.520816 93.1875,114.0625 C95.1458431,117.604184 97.8124831,120.354157 101.1875,122.3125 C104.562517,124.270843 108.374979,125.25 112.625,125.25 Z M261,68.75 L236.25,135 L224.75,135 L205.625,84.75 L186.25,135 L174.75,135 L150.125,68.75 L161.5,68.75 L180.75,121.5 L200.75,68.75 L210.875,68.75 L230.5,121.75 L250.25,68.75 L261,68.75 Z M330.125,102.25 C330.125,103.166671 330.041668,104.374993 329.875,105.875 L276.125,105.875 C276.875004,111.708363 279.437478,116.395816 283.8125,119.9375 C288.187522,123.479184 293.624967,125.25 300.125,125.25 C308.041706,125.25 314.416642,122.58336 319.25,117.25 L325.875,125 C322.874985,128.500018 319.145856,131.166657 314.6875,133 C310.229144,134.833343 305.250027,135.75 299.75,135.75 C292.749965,135.75 286.541694,134.312514 281.125,131.4375 C275.708306,128.562486 271.520848,124.541692 268.5625,119.375 C265.604152,114.208308 264.125,108.375033 264.125,101.875 C264.125,95.4583013 265.562486,89.6666925 268.4375,84.5 C271.312514,79.3333075 275.270808,75.3125144 280.3125,72.4375 C285.354192,69.5624856 291.041635,68.125 297.375,68.125 C303.708365,68.125 309.354142,69.5624856 314.3125,72.4375 C319.270858,75.3125144 323.145819,79.3333075 325.9375,84.5 C328.729181,89.6666925 330.125,95.5833 330.125,102.25 Z M297.375,78.25 C291.624971,78.25 286.812519,79.9999825 282.9375,83.5 C279.062481,87.0000175 276.79167,91.583305 276.125,97.25 L318.625,97.25 C317.95833,91.6666387 315.687519,87.1041844 311.8125,83.5625 C307.937481,80.0208156 303.125029,78.25 297.375,78.25 Z M357.25,79.875 C359.333344,76.0416475 362.416646,73.12501 366.5,71.125 C370.583354,69.12499 375.541637,68.125 381.375,68.125 L381.375,79.75 C380.70833,79.6666663 379.791673,79.625 378.625,79.625 C372.124967,79.625 367.020852,81.5624806 363.3125,85.4375 C359.604148,89.3125194 357.75,94.8332975 357.75,102 L357.75,135 L345.75,135 L345.75,68.75 L357.25,68.75 L357.25,79.875 Z M454.375,102.25 C454.375,103.166671 454.291668,104.374993 454.125,105.875 L400.375,105.875 C401.125004,111.708363 403.687478,116.395816 408.0625,119.9375 C412.437522,123.479184 417.874967,125.25 424.375,125.25 C432.291706,125.25 438.666642,122.58336 443.5,117.25 L450.125,125 C447.124985,128.500018 443.395856,131.166657 438.9375,133 C434.479144,134.833343 429.500027,135.75 424,135.75 C416.999965,135.75 410.791694,134.312514 405.375,131.4375 C399.958306,128.562486 395.770848,124.541692 392.8125,119.375 C389.854152,114.208308 388.375,108.375033 388.375,101.875 C388.375,95.4583013 389.812486,89.6666925 392.6875,84.5 C395.562514,79.3333075 399.520808,75.3125144 404.5625,72.4375 C409.604192,69.5624856 415.291635,68.125 421.625,68.125 C427.958365,68.125 433.604142,69.5624856 438.5625,72.4375 C443.520858,75.3125144 447.395819,79.3333075 450.1875,84.5 C452.979181,89.6666925 454.375,95.5833 454.375,102.25 Z M421.625,78.25 C415.874971,78.25 411.062519,79.9999825 407.1875,83.5 C403.312481,87.0000175 401.04167,91.583305 400.375,97.25 L442.875,97.25 C442.20833,91.6666387 439.937519,87.1041844 436.0625,83.5625 C432.187481,80.0208156 427.375029,78.25 421.625,78.25 Z M532.5,42.25 L532.5,135 L521,135 L521,124.5 C518.33332,128.166685 514.958354,130.958324 510.875,132.875 C506.791646,134.791676 502.291691,135.75 497.375,135.75 C490.958301,135.75 485.208359,134.333347 480.125,131.5 C475.041641,128.666653 471.062514,124.687526 468.1875,119.5625 C465.312486,114.437474 463.875,108.5417 463.875,101.875 C463.875,95.2083 465.312486,89.3333588 468.1875,84.25 C471.062514,79.1666412 475.041641,75.2083475 480.125,72.375 C485.208359,69.5416525 490.958301,68.125 497.375,68.125 C502.125024,68.125 506.49998,69.0208244 510.5,70.8125 C514.50002,72.6041756 517.83332,75.2499825 520.5,78.75 L520.5,42.25 L532.5,42.25 Z M498.375,125.25 C502.541688,125.25 506.333316,124.270843 509.75,122.3125 C513.166684,120.354157 515.833324,117.604184 517.75,114.0625 C519.666676,110.520816 520.625,106.458356 520.625,101.875 C520.625,97.2916437 519.666676,93.2291844 517.75,89.6875 C515.833324,86.1458156 513.166684,83.4166762 509.75,81.5 C506.333316,79.5833238 502.541688,78.625 498.375,78.625 C494.124979,78.625 490.312517,79.5833238 486.9375,81.5 C483.562483,83.4166762 480.895843,86.1458156 478.9375,89.6875 C476.979157,93.2291844 476,97.2916437 476,101.875 C476,106.458356 476.979157,110.520816 478.9375,114.0625 C480.895843,117.604184 483.562483,120.354157 486.9375,122.3125 C490.312517,124.270843 494.124979,125.25 498.375,125.25 Z M622,68.125 C628.416699,68.125 634.166641,69.5416525 639.25,72.375 C644.333359,75.2083475 648.312486,79.1666412 651.1875,84.25 C654.062514,89.3333588 655.5,95.2083 655.5,101.875 C655.5,108.5417 654.062514,114.437474 651.1875,119.5625 C648.312486,124.687526 644.333359,128.666653 639.25,131.5 C634.166641,134.333347 628.416699,135.75 622,135.75 C617.083309,135.75 612.583354,134.791676 608.5,132.875 C604.416646,130.958324 601.04168,128.166685 598.375,124.5 L598.375,135 L586.875,135 L586.875,42.25 L598.875,42.25 L598.875,78.75 C601.54168,75.2499825 604.87498,72.6041756 608.875,70.8125 C612.87502,69.0208244 617.249976,68.125 622,68.125 Z M621,125.25 C625.250021,125.25 629.062483,124.270843 632.4375,122.3125 C635.812517,120.354157 638.479157,117.604184 640.4375,114.0625 C642.395843,110.520816 643.375,106.458356 643.375,101.875 C643.375,97.2916437 642.395843,93.2291844 640.4375,89.6875 C638.479157,86.1458156 635.812517,83.4166762 632.4375,81.5 C629.062483,79.5833238 625.250021,78.625 621,78.625 C616.833312,78.625 613.041684,79.5833238 609.625,81.5 C606.208316,83.4166762 603.541676,86.1458156 601.625,89.6875 C599.708324,93.2291844 598.75,97.2916437 598.75,101.875 C598.75,106.458356 599.708324,110.520816 601.625,114.0625 C603.541676,117.604184 606.208316,120.354157 609.625,122.3125 C613.041684,124.270843 616.833312,125.25 621,125.25 Z M727.875,68.75 L695.75,141.625 C692.916653,148.375034 689.604186,153.124986 685.8125,155.875 C682.020814,158.625014 677.45836,160 672.125,160 C668.874984,160 665.729182,159.479172 662.6875,158.4375 C659.645818,157.395828 657.125,155.87501 655.125,153.875 L660.25,144.875 C663.666684,148.125016 667.624978,149.75 672.125,149.75 C675.041681,149.75 677.479157,148.979174 679.4375,147.4375 C681.395843,145.895826 683.166659,143.250019 684.75,139.5 L686.875,134.875 L657.625,68.75 L670.125,68.75 L693.25,121.75 L716.125,68.75 L727.875,68.75 Z" id="poweredby" fill="#007ADD" fill-rule="nonzero"/>
                                        </g>
                                    </g>
                                </svg>`

            this.loaderIco = document.createElement('img')
            this.loaderIco.alt = 'Loading'
            this.loaderIco.src = `${wpfTenor['WPFOROTENOR_URL']}/assets/ico/spinning.png`
            this.loaderIco.width = 40
            this.loaderIco.height = 40

            this.searchIco = document.createElement('img')
            this.searchIco.alt = 'Submit'
            this.searchIco.src = `${wpfTenor['WPFOROTENOR_URL']}/assets/ico/icon_search.png`
            this.searchIco.width = 40
            this.searchIco.height = 40

            this.wrap = document.createElement('div')
            this.wrap.id = 'wpf-tenor-wrap'
            this.wrap.insertAdjacentHTML('beforeend',
                `
            <div id="wpf-gifs-scroll">
                <div id="wpf-gifs">
                    <div class="wpforo-dialog-loading"></div>
                </div>
            </div>
            <div id="wpf-gif-bar">
                <div id="wpf-gif-hamburger">
                    <img src="${wpfTenor['WPFOROTENOR_URL']}/assets/ico/icon_menu.png" alt="Menu" width="40" height="40">
                </div>
                <form id="wpf-gif-search-form">
                    <label>
                        <input list="trending-searches" id="wpf-gif-search" type="search" name="needle" placeholder="Search Tenor" required>
                        <datalist id="trending-searches"></datalist>
                    </label>
                    <button></button>
                </form>
            </div>`
            )

            this.scroll = this.wrap.querySelector('#wpf-gifs-scroll')

            const wpfDL = this.wrap.querySelector('.wpforo-dialog-loading')
            if (wpfDL) wpfDL.append(this.loaderIco)

            this.searchButton = this.wrap.querySelector('form#wpf-gif-search-form button')
            if (this.searchButton) this.searchButton.append(this.searchIco)

            this.form = this.wrap.querySelector('form#wpf-gif-search-form')
            this.needle = this.form.elements['needle']
        }

        async enableEvents () {
            await this.onSubmit()
            await this.onScroll()
            await this.onMenu()
        }

        async onLoad () {
            await this.fetchTrendingSearches(this.wrap)
            wpforo_dialog_show(this.title, this.wrap, '', 'calc(100vh - 10%)')
            const wpfGifCells = this.wrap.querySelectorAll('#wpf-gifs .wpf-gif-cell')
            if (!wpfGifCells.length) {
                await this.fetchTrending(this.wrap, wpfTenor['limit'], '', wpfTenor['contentfilter'], wpfTenor['anon_id'])
            }
        }

        async onSubmit () {
            if (this.form) {
                this.form.addEventListener('submit', async (e) => {
                    e.preventDefault()
                    this.catsElementHide(this.wrap)
                    this.searchButton.textContent = ''
                    this.searchButton.append(this.loaderIco)
                    await this.fetchSearch(this.wrap, this.needle.value, wpfTenor['limit'], '', wpfTenor['contentfilter'], wpfTenor['anon_id'], wpfTenor['locale'])
                    this.searchButton.textContent = ''
                    this.searchButton.append(this.searchIco)
                })
            }
        }

        async onScroll () {
            if (this.scroll) {
                this.scroll.addEventListener('scroll', async () => {
                    if ((this.scroll.scrollHeight - this.scroll.scrollTop - this.scroll.clientHeight) < 200) {
                        const dataset = this.dataset.get(this.wrap)
                        if (!dataset['inuse']) {
                            if (dataset['type'] === 'trending') {
                                await this.fetchTrending(this.wrap, dataset['limit'], dataset['next'], dataset['contentfilter'], dataset['anon_id'])
                            } else {
                                await this.fetchSearch(this.wrap, dataset['needle'], dataset['limit'], dataset['next'], dataset['contentfilter'], dataset['anon_id'], dataset['locale'])
                            }
                        }
                    }
                })
            }
        }

        /**
         * @returns {Array}
         */
        async fetchCats () {
            if (wpfTenor['cats'] && Array.isArray(wpfTenor['cats']) && wpfTenor['cats'].length) return wpfTenor['cats']
            try {
                const apiUrl = `https://g.tenor.com/v1/categories?key=${wpfTenor['key']}&locale=${wpfTenor['locale']}&anon_id=${wpfTenor['anon_id']}`
                let r = await fetch(apiUrl)
                if (r.ok && r.status === 200) {
                    r = await r.json()
                    const cats = [{ name: 'Categories', subcategories: [] }]
                    r.tags.forEach((subcat) => {
                        cats[0]['subcategories'].push({ name: subcat['searchterm'] })
                    })
                    return cats
                }
            } catch (e) { console.error(e) }
        }

        /**
         *
         * @param {Element} wrapElement
         */
        catsElementExist (wrapElement) {
            return !!wrapElement.querySelector('#wpf-gifs-cats')
        }

        /**
         *
         * @param {Element} wrapElement
         */
        catsElementShow (wrapElement) {
            const catsElement = wrapElement.querySelector('#wpf-gifs-cats')
            if (catsElement) catsElement.style.display = 'block'
        }

        /**
         *
         * @param {Element} wrapElement
         */
        catsElementHide (wrapElement) {
            const catsElement = wrapElement.querySelector('#wpf-gifs-cats')
            if (catsElement) catsElement.style.display = 'none'
        }

        /**
         *
         * @param {Element} wrapElement
         */
        catsElementToggle (wrapElement) {
            const catsElement = wrapElement.querySelector('#wpf-gifs-cats')
            if (catsElement.style.display === 'none') {
                catsElement.style.display = 'block'
            } else {
                catsElement.style.display = 'none'
            }
        }

        generateColor () {
            return '#007add'
            /*return [
             "#399DEF","#007ADD","#9B9B9B","#EFF6F9","#3F3F3F",
             "#76B7EE", "#90B58C","#9686B3"
             ][Math.floor(Math.random() * 8)];*/
        }

        onMenu () {
            const menu = this.wrap.querySelector('#wpf-gif-hamburger')
            if (menu) {
                menu.addEventListener('click', async () => {
                    if (this.catsElementExist(this.wrap)) {
                        this.catsElementToggle(this.wrap)
                    } else {
                        const cats = await this.fetchCats()
                        if (cats && Array.isArray(cats) && cats.length) {
                            let list = ''
                            cats.forEach((cat) => {
                                list += `<li><div class="wpf-gif-cat" style="background-color: ${this.generateColor()};">${cat['name']}</div>`
                                let sublist = ''
                                if (cat['subcategories'] && Array.isArray(cat['subcategories']) && cat['subcategories'].length) {
                                    cat['subcategories'].forEach((subcat) => {
                                        sublist += `<li><div class="wpf-gif-subcat">${subcat['name']}</div></li>`
                                    })
                                }
                                if (sublist) list += `<ul>${sublist}</ul>`
                                list += `</li>`
                            })
                            if (list) {
                                list = `<ul id="wpf-gifs-cats">${list}</ul>`
                                this.wrap.insertAdjacentHTML('afterbegin', list)
                                this.initCatsAccordion(this.wrap)
                                this.initCatsListener(this.wrap)
                            }
                        }
                    }
                })
            }
        }

        /**
         *
         * @param {Element} wrapElement
         * @param {Object} gif
         */
        imgSetDatasets (wrapElement, gif) {
            // console.log(gif);

            wrapElement['dataset']['tenorid'] = 'tenor_' + gif['id']
            wrapElement['dataset']['alt'] = gif['title']
            wrapElement['dataset']['title'] = gif['title']

            wrapElement['dataset']['analsent'] = `https://g.tenor.com/v1/registershare?id=${gif['id']}&key=${wpfTenor['key']}`

            wrapElement['dataset']['src'] = gif['media'][0]['tinygif']['url']
            wrapElement['dataset']['srcwidth'] = gif['media'][0]['tinygif']['dims'][0]
            wrapElement['dataset']['srcheight'] = gif['media'][0]['tinygif']['dims'][1]

            wrapElement['dataset']['still'] = gif['media'][0]['tinygif']['preview']
            wrapElement['dataset']['stillwidth'] = gif['media'][0]['tinygif']['dims'][0]
            wrapElement['dataset']['stillheight'] = gif['media'][0]['tinygif']['dims'][1]
        }

        /**
         * @param {Element} wrapElement
         */
        initGifSendListener (wrapElement) {
            wrapElement.addEventListener('click', () => {
                wpforo_editor.insert_content(this.makeFrontViewHtml(wrapElement))
                wpforo_dialog_hide()
            })
        }

        /**
         *
         * @param {Element} wrapElement
         */
        makeFrontViewHtml (wrapElement) {
            const data = wrapElement['dataset']
            if (wpforo_editor.is_tinymce()) {
                return `
                    <figure class="wpf-gif-figure" contenteditable="false" 
                            data-tenorid="${data['tenorid']}"
                            data-analsent="${data['analsent']}">
                        <img style="background-color: ${this.generateColor()};"
                             width="${data['srcwidth']}"
                             height="${data['srcheight']}"
                             src="${data['src']}"
                             title="${data['title']}"
                             alt="${data['alt']}"
                             data-src="${data['src']}"
                             data-srcwidth="${data['srcwidth']}"
                             data-srcheight="${data['srcheight']}"
                             data-still="${data['still']}"
                             data-stillwidth="${data['stillwidth']}"
                             data-stillheight="${data['stillheight']}"
                        >
                    </figure>
                `
            } else {
                return `[wpftenor tenorid="${data['tenorid']}" title="${data['title']}" alt="${data['alt']}" analsent="${data['analsent']}" src="${data['src']}" srcwidth="${data['srcwidth']}" srcheight="${data['srcheight']}" still="${data['still']}" stillwidth="${data['stillwidth']}" stillheight="${data['stillheight']}"]`
            }
        }

        /**
         * @param {Element} wrapElement
         */
        initTagsListener (wrapElement) {
            if (this.needle) {
                const tags = wrapElement.querySelectorAll('.wpf-gif-tag')
                if (tags.length) {
                    [...tags].forEach((tag) => {
                        tag.addEventListener('click', () => {
                            let text = tag.innerText.trim()
                            text = text.substr(1)
                            this.needle.value = text
                            this.searchButton.click()
                        })
                    })
                }
            }
        }

        /**
         * @param {Element} wrapElement
         */
        initCatsAccordion (wrapElement) {
            let cats = wrapElement.querySelectorAll('#wpf-gifs-cats .wpf-gif-cat')
            if (cats) {
                cats = [...cats]
                if (cats.length === 1) {
                    cats[0].style.cursor = 'auto'
                    cats[0]['parentNode'].classList.add('wpf-gif-cat-active')
                } else {
                    cats.forEach((cat) => {
                        const li = cat.parentNode
                        cat.addEventListener('click', () => {
                            const activeLis = wrapElement.querySelectorAll('#wpf-gifs-cats > li.wpf-gif-cat-active')
                            if (activeLis) {
                                activeLis.forEach((ali) => {
                                    if (li !== ali) ali.classList.remove('wpf-gif-cat-active')
                                })
                            }
                            li.classList.toggle('wpf-gif-cat-active')
                        })
                    })
                }
            }
        }

        /**
         * @param {Element} wrapElement
         */
        initCatsListener (wrapElement) {
            if (this.needle) {
                const cats = wrapElement.querySelectorAll('.wpf-gif-subcat')
                if (cats.length) {
                    [...cats].forEach((cat) => {
                        cat.addEventListener('click', () => {
                            this.needle.value = cat.innerText.trim()
                            this.searchButton.click()
                        })
                    })
                }
            }
        }

        /**
         * @param {Element} wrapElement
         */
        clear (wrapElement) {
            wrapElement.querySelector('#wpf-gifs').textContent = ''
            wpfmasonry.fix()
        }

        hasMore (apiUrl, status) {
            if (status !== undefined) {
                status = !!status
                this.logs.set(apiUrl, status)
                return status
            }
            return !this.logs.has(apiUrl) || this.logs.get(apiUrl)
        }

        /**
         * @param {Element} wrapElement
         */
        async fetchTrendingSearches (wrapElement) {
            const datalist = wrapElement.querySelector('datalist#trending-searches')
            if (datalist) {
                if (!this.trendingSearches.length) {
                    try {
                        const apiUrl = `https://g.tenor.com/v1/trending_terms?key=${wpfTenor['key']}&locale=${wpfTenor['locale']}&anon_id=${wpfTenor['anon_id']}`
                        let r = await fetch(apiUrl)
                        if (r.ok && r.status === 200) {
                            r = await r.json()
                            this.trendingSearches = r.results
                        }
                    } catch (e) { console.error(e) }
                }

                if (this.trendingSearches.length) {
                    let options = ''
                    this.trendingSearches.forEach((phrase) => {
                        options += `<option value="${phrase}">`
                    })
                    datalist.innerHTML = options
                }
            }
        }

        /**
         *
         * @param {Element} wrapElement
         * @param {number|string}  limit
         * @param {number|string}  pos
         * @param {string}  contentfilter
         * @param {string}  anon_id
         */
        async fetchTrending (wrapElement, limit = 25, pos = '', contentfilter = 'off', anon_id = '') {
            limit = parseInt(limit)
            try {
                const apiUrl = `https://g.tenor.com/v1/trending?key=${wpfTenor['key']}&limit=${limit}&pos=${pos}&contentfilter=${contentfilter}&anon_id=${anon_id}&locale=${wpfTenor['locale']}&media_filter=minimal`
                if (this.hasMore(apiUrl.replace(/&pos=\d+/iu, `&pos=${(parseInt(pos) - limit)}`))) {
                    this.dataset.setInuse(wrapElement, true)
                    let r = await fetch(apiUrl)
                    if (r.ok && r.status === 200) {
                        r = await r.json()
                        this.hasMore(apiUrl, r['next'])
                        this.dataset.set(wrapElement, 'trending', '', limit, r['next'], contentfilter, anon_id)
                        if (!pos) this.clear(wrapElement)
                        this.print(wrapElement, r.results)
                    }
                    this.dataset.setInuse(wrapElement, false)
                }
            } catch (e) { console.error(e) }
        }

        /**
         *
         * @param {Element} wrapElement
         * @param {string}  needle
         * @param {number|string}  limit
         * @param {number|string}  pos
         * @param {string}  contentfilter
         * @param {string}  anon_id
         * @param {string}  locale
         */
        async fetchSearch (wrapElement, needle, limit = 25, pos = '', contentfilter = 'off', anon_id = '', locale = '') {
            needle = needle.trim()
            if (!needle) return
            limit = parseInt(limit)
            try {
                const apiUrl = `https://g.tenor.com/v1/search?key=${wpfTenor['key']}&q=${needle}&limit=${limit}&pos=${pos}&contentfilter=${contentfilter}&anon_id=${anon_id}&locale=${locale}&media_filter=minimal`
                if (this.hasMore(apiUrl.replace(/&pos=\d+/iu, `&pos=${(parseInt(pos) - limit)}`))) {
                    this.dataset.setInuse(wrapElement, true)
                    let r = await fetch(apiUrl)
                    if (r.ok && r.status === 200) {
                        r = await r.json()
                        this.hasMore(apiUrl, r['next'])
                        this.dataset.set(wrapElement, 'search', needle, limit, r['next'], contentfilter, anon_id, locale)
                        if (!pos) this.clear(wrapElement)
                        this.print(wrapElement, r.results)
                    }
                    this.dataset.setInuse(wrapElement, false)
                }
            } catch (e) { console.error(e) }
        }

        /**
         * @param {Element} wrapElement
         * @param {Array}   gifs
         */
        print (wrapElement, gifs) {
            const wpfGifsWrap = wrapElement.querySelector('#wpf-gifs')
            if (wpfGifsWrap) {
                gifs.forEach((gif) => {
                    let img = gif.media && gif.media[0]
                    if (img) {
                        let gifCell = document.createElement('div')
                        gifCell.classList.add('wpf-gif-cell')
                        gifCell.style.width = img['tinygif']['dims'][0] + 'px'

                        let imgWrap = document.createElement('div')
                        imgWrap.classList.add('wpf-gif-img-wrap')
                        imgWrap.style.width = '100%'
                        imgWrap.style.height = img['tinygif']['dims'][1] + 'px'
                        imgWrap.style.backgroundColor = this.generateColor()
                        this.imgSetDatasets(imgWrap, gif)

                        let tagsWrap = document.createElement('div')
                        tagsWrap.classList.add('wpf-gif-tags-wrap')
                        tagsWrap.style.width = '100%'
                        tagsWrap.style.whiteSpace = 'pre-wrap'
                        let tags5 = (gif.tags || []).filter(tag => !!tag.trim()).slice(0, 5)
                        if (tags5.length) tagsWrap.innerHTML = '<span class="wpf-gif-tag">#' + tags5.join('</span> <span class="wpf-gif-tag">#') + '</span>'

                        gifCell.append(imgWrap)
                        this.initGifSendListener(imgWrap)

                        gifCell.append(tagsWrap)
                        this.initTagsListener(tagsWrap)

                        wpfGifsWrap.append(gifCell)
                        wpfmasonry.fix()

                        this.addImg(
                            img['tinygif'].url,
                            img['tinygif']['dims'][0],
                            img['tinygif']['dims'][1],
                            gif['title'],
                            'tenor_' + gif.id
                        ).then(
                            /**
                             * @param {Element} img
                             */
                            (img) => {
                                imgWrap.append(img)
                            }
                        )
                    }
                })
            }
        }

        addImg (src, width = 200, height = 0, title = '', id = '', cls = 'wpf-gif') {
            return new Promise((resolve, reject) => {
                let img = new Image()
                img.onload = () => resolve(img)
                img.onerror = reject
                img.src = src
                img.width = width
                img.title = img.alt = title
                img.id = id
                img.classList.add(cls)
                if (height) img.height = height
            })
        }
    }

    class wpfGifsDataset {
        constructor (main) {
            this.main = main
        }

        /**
         * @param {Element} wrapElement
         * @param {Boolean} inuse
         */
        setInuse (wrapElement, inuse = true) {
            if (this.main.scroll) this.main.scroll.dataset['inuse'] = (inuse ? 1 : 0)
        }

        /**
         * @param {Element} wrapElement
         * @param {string} type
         * @param {string} needle
         * @param {number} limit
         * @param {string} next
         * @param {string} contentfilter
         * @param {string} anon_id
         * @param {string} locale
         */
        set (wrapElement, type = 'trending', needle = '', limit = 25, next = '', contentfilter = 'off', anon_id = '', locale = '') {
            if (this.main.scroll) {
                this.main.scroll.dataset['type'] = type
                this.main.scroll.dataset['needle'] = needle
                this.main.scroll.dataset['limit'] = limit
                this.main.scroll.dataset['next'] = next
                this.main.scroll.dataset['contentfilter'] = contentfilter
                this.main.scroll.dataset['anon_id'] = anon_id
                this.main.scroll.dataset['locale'] = locale
            }
        }

        /**
         * @param {Element} wrapElement
         */
        get (wrapElement) {
            const datavars = {
                inuse: 0,
                type: 'trending',
                needle: '',
                limit: 25,
                next: '',
                contentfilter: 'off',
                anon_id: '',
                locale: '',
            }

            if (this.main.scroll) {
                if (this.main.scroll.dataset['inuse']) datavars['inuse'] = parseInt(this.main.scroll.dataset['inuse'])
                if (this.main.scroll.dataset['type']) datavars['type'] = this.main.scroll.dataset['type'].trim()
                if (this.main.scroll.dataset['needle']) datavars['needle'] = this.main.scroll.dataset['needle'].trim()
                if (this.main.scroll.dataset['limit']) datavars['limit'] = parseInt(this.main.scroll.dataset['limit'])
                if (this.main.scroll.dataset['next']) datavars['next'] = this.main.scroll.dataset['next']
                if (this.main.scroll.dataset['contentfilter']) datavars['contentfilter'] = this.main.scroll.dataset['contentfilter']
                if (this.main.scroll.dataset['anon_id']) datavars['anon_id'] = this.main.scroll.dataset['anon_id']
                if (this.main.scroll.dataset['locale']) datavars['locale'] = this.main.scroll.dataset['locale']
            }

            return datavars
        }
    }

    window.addEventListener('load', () => {
        const tenor = new Tenor

        const wpfg = document.querySelectorAll('#wpforo #wpforo-wrap .wpf-tenor-button-wrap .wpf-tenor-button')
        if (wpfg) {
            [...wpfg].forEach(el => {
                el.addEventListener('click', async () => {
                    await tenor.onLoad()
                })
            })
        }

        document.addEventListener('wpforo_topic_portable_form', function (e) {
            if (e.detail[0]) {
                const wpfg = e.detail[0].querySelector('.wpf-tenor-button-wrap .wpf-tenor-button')
                if (wpfg) {
                    wpfg.addEventListener('click', async () => {
                        await tenor.onLoad()
                    })
                }
            }
        })

        const lazyImgs = document.querySelectorAll('img.wpf-gif-lazy[data-src]')
        if (lazyImgs) {
            [...lazyImgs].forEach(img => {
                img.src = img['dataset']['src']
            })
        }

        document.addEventListener('wpforo_post_submit', function (e) {
            if (wpfTenor['anon_id']) {
                const content = wpforo_editor.get_content('raw', e.detail.textareaid)
                const el = document.createElement('div')
                el.insertAdjacentHTML('afterbegin', content)
                const figures = el.querySelectorAll('figure[data-tenorid][data-analsent]')
                if (figures) {
                    [...figures].forEach(async (figure) => {
                        const analsent = figure['dataset']['analsent']
                        if (analsent) {
                            const apiUrl = `${analsent}&anon_id=${wpfTenor['anon_id']}`
                            await fetch(apiUrl)
                        }
                    })
                }
            }
        })

    })
})()
