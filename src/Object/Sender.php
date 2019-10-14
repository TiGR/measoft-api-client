<?php

namespace Measoft\Object;

class Sender extends AbstractObject
{
	/**
	 * @var string $company Название компании отправителя
	 */
	protected $company;

	/**
	 * @var string $person Контактное лицо отправителя
	 */
	protected $person;

	/**
	 * @var string $phone Телефон, Email
	 */
	protected $phone;

	/**
	 * @var array $contacts Дополнительные контакты
	 */
	protected $contacts;

	/**
	 * @var Town $town Город
	 */
	protected $town;

	/**
	 * @var string $address Адрес
	 */
	protected $address;

	/**
	 * @var string $date Дата забора ("YYYY-MM-DD")
	 */
	protected $date;

	/**
	 * @var string $timeMin Минимальное время забора ("HH:MM")
	 */
	protected $timeMin;

	/**
	 * @var string $timeMax Максимальное время забора ("HH:MM")
	 */
	protected $timeMax;

	public static function getFromXml(\SimpleXMLElement $xml, bool $fromNode = true): self
	{
		if (isset($xml->contacts)) {
			foreach ($xml->contacts->children() as $contact) {
				$contacts[] = [
					'name'  => $contact->getName(),
					'value' => (string) $contact,
				];
			}
		}

		if (isset($xml->town)) {
			$town = Town::getFromXml($xml->town, false);
		}

		$params = [
			'company'  => self::extractXmlValue($xml, 'company', $fromNode),
			'person'   => self::extractXmlValue($xml, 'person', $fromNode),
			'phone'    => self::extractXmlValue($xml, 'phone', $fromNode),
			'address'  => self::extractXmlValue($xml, 'address', $fromNode),
			'date'     => self::extractXmlValue($xml, 'date', $fromNode),
			'timeMin'  => self::extractXmlValue($xml, 'time_min', $fromNode),
			'timeMax'  => self::extractXmlValue($xml, 'time_max', $fromNode),
			'contacts' => $contacts ?? [],
			'town'     => $town     ?? null,
		];

		return new Sender($params);
	}

	/**
	 * @return string Название компании отправителя
	 */ 
	public function getCompany(): ?string
	{
		return $this->company;
	}

	/**
	 * @return string Контактное лицо отправителя
	 */ 
	public function getPerson(): ?string
	{
		return $this->person;
	}

	/**
	 * @return string Телефон, Email
	 */ 
	public function getPhone(): ?string
	{
		return $this->phone;
	}

	/**
	 * @return array Дополнительные контакты
	 */ 
	public function getContacts(): array
	{
		return $this->contacts;
	}

	/**
	 * @return Town Город
	 */ 
	public function getTown(): ?Town
	{
		return $this->town;
	}

	/**
	 * @return string Адрес
	 */ 
	public function getAddress(): ?string
	{
		return $this->address;
	}

	/**
	 * @return string Дата забора ("YYYY-MM-DD")
	 */ 
	public function getDate(): ?string
	{
		return $this->date;
	}

	/**
	 * @return string Минимальное время забора ("HH:MM")
	 */ 
	public function getTimeMin(): ?string
	{
		return $this->timeMin;
	}

	/**
	 * @return string Максимальное время забора ("HH:MM")
	 */ 
	public function getTimeMax(): ?string
	{
		return $this->timeMax;
	}
}