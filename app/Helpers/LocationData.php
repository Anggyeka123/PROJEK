<?php

namespace App\Helpers;

class LocationData
{
    /**
     * Daftar lengkap 38 provinsi Indonesia dan kota/kabupaten di dalamnya.
     * Provinsi diurutkan secara alfabetik A-Z.
     */
    public static function getIndonesianProvinces(): array
    {
        $provinces = [
            'Aceh' => [
                'Banda Aceh', 'Aceh Besar', 'Aceh Barat', 'Aceh Barat Daya', 'Aceh Jaya',
                'Aceh Pidie', 'Pidie Jaya', 'Aceh Utara', 'Lhokseumawe', 'Aceh Timur',
                'Aceh Tamiang', 'Langsa', 'Gayo Lues', 'Aceh Selatan', 'Subulussalam'
            ],
            'Bali' => [
                'Denpasar', 'Badung', 'Bangli', 'Buleleng', 'Gianyar', 'Jembrana', 'Karangasem', 'Klungkung', 'Tabanan'
            ],
            'Banten' => [
                'Serang', 'Cilegon', 'Tangerang', 'Tangerang Selatan', 'Lebak', 'Pandeglang'
            ],
            'Bengkulu' => [
                'Bengkulu', 'Curup', 'Rejang Lebong', 'Bengkulu Utara', 'Bengkulu Tengah', 'Bengkulu Selatan',
                'Kaur', 'Seluma', 'Muko Muko', 'Lebong'
            ],
            'DI Yogyakarta' => [
                'Yogyakarta', 'Sleman', 'Bantul', 'Gunung Kidul', 'Kulon Progo'
            ],
            'DKI Jakarta' => [
                'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Selatan',
                'Kepulauan Seribu'
            ],
            'Gorontalo' => [
                'Gorontalo', 'Gorontalo Utara', 'Boalemo', 'Bone Bolango', 'Pohuwato'
            ],
            'Jambi' => [
                'Jambi', 'Sungai Penuh', 'Muaro Jambi', 'Tanjab Timur', 'Tanjab Barat', 'Tebo',
                'Bungo', 'Kerinci', 'Merangin', 'Sarolangun'
            ],
            'Jawa Barat' => [
                'Bandung', 'Bekasi', 'Bogor', 'Depok', 'Cimahi', 'Tasikmalaya', 'Banjar',
                'Cianjur', 'Garut', 'Sumedang', 'Subang', 'Purwakarta', 'Karawang', 'Indramayu',
                'Majalengka', 'Kuningan', 'Cirebon', 'Sukabumi', 'Pangandaran', 'Bandung Barat',
                'Bekasi Barat', 'Bekasi Timur', 'Bogor Utara', 'Bogor Timur', 'Bogor Barat'
            ],
            'Jawa Tengah' => [
                'Semarang', 'Pekalongan', 'Tegal', 'Salatiga', 'Surakarta', 'Magelang',
                'Cilacap', 'Banyumas', 'Purbalingga', 'Banjarnegara', 'Kebumen', 'Wonosobo',
                'Magelang', 'Boyolali', 'Klaten', 'Sukoharjo', 'Wonogiri', 'Karanganyar',
                'Sragen', 'Rembang', 'Pati', 'Kudus', 'Jepara', 'Blora', 'Demak', 'Grobogan',
                'Ngawi', 'Magetan', 'Nganjuk', 'Jombang', 'Brebes', 'Tembilahan'
            ],
            'Jawa Timur' => [
                'Surabaya', 'Gresik', 'Sidoarjo', 'Lamongan', 'Tuban', 'Bojonegoro',
                'Sumenep', 'Pamekasan', 'Sampang', 'Bangkalan', 'Jombang', 'Mojokerto',
                'Nganjuk', 'Magetan', 'Ngawi', 'Ponorogo', 'Trenggalek', 'Tulungagung',
                'Blitar', 'Kediri', 'Madiun', 'Malang', 'Batu', 'Pasuruan', 'Probolinggo',
                'Lumajang', 'Jember', 'Banyuwangi', 'Bondowoso', 'Situbondo', 'Pacitan'
            ],
            'Kalimantan Barat' => [
                'Pontianak', 'Singkawang', 'Sambas', 'Mempawah', 'Bengkayang', 'Sanggau', 'Sekadau',
                'Kapuas Hulu', 'Melawi', 'Kayong Utara', 'Kubu Raya'
            ],
            'Kalimantan Selatan' => [
                'Banjarmasin', 'Banjarbaru', 'Tanah Laut', 'Kota Baru', 'Banjar', 'Barito Kuala',
                'Tapin', 'Hulu Sungai Utara', 'Hulu Sungai Selatan', 'Hulu Sungai Tengah',
                'Tabalong', 'Paser'
            ],
            'Kalimantan Tengah' => [
                'Palangka Raya', 'Kotawaringin Timur', 'Kotawaringin Barat', 'Kapuas', 'Katingan',
                'Seruyan', 'Sukamara', 'Lamandau', 'Gunung Mas', 'Murung Raya', 'Barito Utara',
                'Barito Timur', 'Barito Selatan'
            ],
            'Kalimantan Timur' => [
                'Samarinda', 'Balikpapan', 'Bontang', 'Berau', 'Kutai Kartanegara', 'Kutai Barat',
                'Paser', 'Penajam Paser Utara', 'Penajam Paser'
            ],
            'Kalimantan Utara' => [
                'Tarakan', 'Tarat', 'Bulungan', 'Malinau', 'Nunukan', 'Tanjung Selor'
            ],
            'Kepulauan Bangka Belitung' => [
                'Pangkal Pinang', 'Bangka', 'Bangka Tengah', 'Bangka Barat', 'Bangka Selatan',
                'Belitung', 'Belitung Timur'
            ],
            'Kepulauan Riau' => [
                'Batam', 'Tanjung Pinang', 'Bintan', 'Karimun', 'Natuna', 'Anambas'
            ],
            'Lampung' => [
                'Bandar Lampung', 'Metro', 'Lampung Tengah', 'Lampung Utara', 'Lampung Timur', 'Lampung Selatan',
                'Lampung Barat', 'Tanggamus', 'Pesawaran', 'Pringsewu', 'Way Kanan', 'Mesuji', 'Tulang Bawang',
                'Tulang Bawang Barat', 'Pesisir Barat'
            ],
            'Maluku' => [
                'Ambon', 'Tual', 'Maluku Tengah', 'Buru', 'Buru Selatan', 'Seram Bagian Barat',
                'Seram Bagian Timur', 'Maluku Utara', 'Halmahera Selatan', 'Halmahera Tengah',
                'Halmahera Utara', 'Halmahera Barat', 'Pulau Morotai', 'Kepulauan Sula'
            ],
            'Maluku Utara' => [
                'Ternate', 'Tidore Kepulauan', 'Halmahera Selatan', 'Halmahera Tengah',
                'Halmahera Utara', 'Halmahera Barat', 'Pulau Morotai', 'Kepulauan Sula'
            ],
            'Nusa Tenggara Barat' => [
                'Mataram', 'Bima', 'Lombok Utara', 'Lombok Tengah', 'Lombok Timur', 'Lombok Barat',
                'Sumbawa', 'Sumbawa Barat', 'Dompu'
            ],
            'Nusa Tenggara Timur' => [
                'Kupang', 'Kota Kupang', 'Timor Tengah Utara', 'Timor Tengah Selatan', 'Timor Leste',
                'Belu', 'Alor', 'Lembata', 'Flores Timur', 'Ende', 'Ngada', 'Manggarai',
                'Manggarai Barat', 'Manggarai Timur', 'Rote Ndao', 'Sabu Raijua'
            ],
            'Papua' => [
                'Jayapura', 'Abepura', 'Jayapura Kota', 'Keerom', 'Gialimo', 'Ifar Dempwolak',
                'Sarmi', 'Pantai Cenderawasih', 'Waropen', 'Mappi', 'Asmat', 'Yahukimo',
                'Pegunungan Bintang', 'Tolikara', 'Mamberamo Raya', 'Mamberamo Tengah', 'Yalimo',
                'Intan Jaya', 'Deiyai', 'Heluk', 'Jayawijaya', 'Lanny Jaya', 'Nduga'
            ],
            'Papua Barat' => [
                'Manokwari', 'Sorong', 'Sorong Selatan', 'Raja Ampat', 'Tambrauw', 'Maybrat',
                'Arfak', 'Anggi', 'Kaimana'
            ],
            'Papua Barat Daya' => [
                'Fakfak', 'Kaimana', 'Sorong', 'Sorong Selatan'
            ],
            'Papua Pegunungan' => [
                'Jayapura', 'Tiom', 'Oksibil', 'Karubaga', 'Wamena', 'Mulia'
            ],
            'Papua Selatan' => [
                'Merauke', 'Bade', 'Maro', 'Bovendigul'
            ],
            'Papua Tengah' => [
                'Nabire', 'Yapen Waropen', 'Supiori', 'Teluk Wondama', 'Memberamo Raya'
            ],
            'Papua Utara' => [
                'Sarmi', 'Jayapura', 'Waris', 'Keerom'
            ],
            'Riau' => [
                'Pekanbaru', 'Dumai', 'Kampar', 'Indragiri Hulu', 'Indragiri Hilir', 'Kuantan Singingi',
                'Pelalawan', 'Siak', 'Bengkalis', 'Rokan Hilir', 'Rokan Hulu', 'Kepulauan Meranti'
            ],
            'Sulawesi Barat' => [
                'Mamuju', 'Mamuju Utara', 'Polewali Mandar', 'Pasangkayu', 'Majene'
            ],
            'Sulawesi Selatan' => [
                'Makassar', 'Palopo', 'Parepare', 'Luwu', 'Luwu Utara', 'Luwu Timur',
                'Toraja Utara', 'Toraja', 'Gowa', 'Takalar', 'Jeneponto', 'Bantaeng',
                'Bulukumba', 'Sinjai', 'Bone', 'Soppeng', 'Wajo', 'Sidrap', 'Pinrang', 'Enrekang', 'Barru'
            ],
            'Sulawesi Tengah' => [
                'Palu', 'Donggala', 'Sigi', 'Parigi Moutong', 'Tojo Una-Una', 'Poso',
                'Banggai', 'Banggai Kepulauan', 'Banggai Laut', 'Morowali', 'Morowali Utara'
            ],
            'Sulawesi Tenggara' => [
                'Kendari', 'Baubau', 'Konawe', 'Konawe Utara', 'Konawe Selatan', 'Konawe Kepulauan',
                'Muna', 'Muna Barat', 'Buton', 'Buton Utara', 'Buton Tengah', 'Buton Selatan',
                'Wakatobi'
            ],
            'Sulawesi Utara' => [
                'Manado', 'Bitung', 'Tomohon', 'Minahasa', 'Minahasa Utara', 'Minahasa Selatan',
                'Minahasa Tenggara', 'Bolaang Mongondow', 'Bolaang Mongondow Utara', 'Bolaang Mongondow Timur',
                'Bolaang Mongondow Selatan', 'Kepulauan Sangihe', 'Kepulauan Talaud'
            ],
            'Sumatera Barat' => [
                'Padang', 'Bukittinggi', 'Pariaman', 'Payakumbuh', 'Sawahlunto', 'Solok', 'Padang Panjang',
                'Pesisir Selatan', 'Solok Selatan', 'Lima Puluh Kota', 'Agam', 'Tanah Datar', 'Padang Lawas',
                'Padang Lawas Utara', 'Dharmasraya', 'Kepulauan Mentawai'
            ],
            'Sumatera Selatan' => [
                'Palembang', 'Prabumulih', 'Lubuk Linggau', 'Ogan Komering Ulu', 'Ogan Komering Ilir',
                'Ogan Komering Ulu Timur', 'Ogan Komering Ulu Selatan', 'Muara Enim', 'Lahat', 'Musi Banyuasin',
                'Musi Rawas', 'Banyuasin', 'Empat Lawang', 'Penukal Abab Lematang Ilir'
            ],
            'Sumatera Utara' => [
                'Medan', 'Binjai', 'Pematang Siantar', 'Tebing Tinggi', 'Simalungun',
                'Asahan', 'Labuhan Batu', 'Deli Serdang', 'Langkat', 'Nias',
                'Humbang Hasundutan', 'Tapanuli Utara', 'Tapanuli Tengah', 'Tapanuli Selatan',
                'Mandailing Natal', 'Nias Utara', 'Nias Barat', 'Labuhan Batu Utara', 'Labuhan Batu Selatan'
            ],
        ];
        
        // Sort by key (province name) alphabetically
        ksort($provinces);
        
        return $provinces;
    }

    /**
     * Daftar lengkap negara dunia.
     */
    public static function getCountries(): array
    {
        return [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda',
            'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain',
            'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
            'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria',
            'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde',
            'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros',
            'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Czechia',
            'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador',
            'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia',
            'Fiji', 'Finland', 'France', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana',
            'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
            'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran',
            'Iraq', 'Ireland', 'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jordan',
            'Kazakhstan', 'Kenya', 'Kiribati', 'Korea', 'Kosovo', 'Kuwait', 'Kyrgyzstan',
            'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein',
            'Lithuania', 'Luxembourg', 'Macao', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives',
            'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico',
            'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique',
            'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua',
            'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan',
            'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines',
            'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis',
            'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
            'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles',
            'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia',
            'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan',
            'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania',
            'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia',
            'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates',
            'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City',
            'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
        ];
    }
}
