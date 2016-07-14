<?php

/*
 * This file is part of the DigitalOceanV2 library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOceanV2\Api;

use DigitalOceanV2\Entity\Volume as VolumeEntity;

/**
 * @author Yassir Hannoun <yassir.hannoun@gmail.com>
 */
class Volume extends AbstractApi
{
    /**
     * @param string $regionSlug restricts results to volumes available in a specific region.
     *
     * @return VolumeEntity[] Lists all of the Block Storage volumes available.
     */
    public function getAll($regionSlug = null)
    {
        $regionQueryParameter = is_null($regionSlug) ? '' : sprintf('&region=%s', $regionSlug);
        $volumes = $this->adapter->get(sprintf('%s/volumes?per_page=%d%s', $this->endpoint, 200, $regionQueryParameter));

        $volumes = json_decode($volumes);

        $this->extractMeta($volumes);

        return array_map(function ($volume) {
            return new VolumeEntity($volume);
        }, $volumes->volumes);
    }

    /**
     * @param string $driveName  restricts results to volumes with the specified name.
     * @param string $regionSlug restricts results to volumes available in a specific region.
     *
     * @return VolumeEntity[] Lists all of the Block Storage volumes available.
     */
    public function getByNameAndRegion($driveName, $regionSlug)
    {
        $volumes = $this->adapter->get(sprintf('%s/volumes?per_page=%d&region=%s&name=%s', $this->endpoint, 200, $regionSlug, $driveName));

        $volumes = json_decode($volumes);

        $this->extractMeta($volumes);

        return array_map(function ($volume) {
            return new VolumeEntity($volume);
        }, $volumes->volumes);
    }

    /**
     * @param string $id
     *
     * @return VolumeEntity the Block Storage volume with the specified id.
     */
    public function getById($id)
    {
        $volume = $this->adapter->get(sprintf('%s/volumes/%s?per_page=%d', $this->endpoint, $id, 200));

        $volume = json_decode($volume);

        return new VolumeEntity($volume->volume);
    }

    /**
     * @param string $name            A human-readable name for the Block Storage volume.
     * @param string $description     Free-form text field to describe a Block Storage volume.
     * @param string $sizeInGigabytes The size of the Block Storage volume in GiB.
     * @param string $regionSlug      The region where the Block Storage volume will be created.
     *
     * @throws HttpException
     * 
     * @return VolumeEntity the Block Storage volume that was created.
     */
    public function create($name, $description, $sizeInGigabytes, $regionSlug)
    {
        $data = [
            'size_gigabytes' => $sizeInGigabytes,
            'name' => $name,
            'description' => $description,
            'region' => $regionSlug,
        ];

        $volume = $this->adapter->post(sprintf('%s/volumes', $this->endpoint), $data);

        $volume = json_decode($volume);

        return new VolumeEntity($volume->volume);
    }

    /**
     * @param string $id
     *
     * @throws HttpException
     */
    public function delete($id)
    {
        $this->adapter->delete(sprintf('%s/volumes/%s', $this->endpoint, $id));
    }

    /**
     * @param string $driveName  restricts the search to volumes with the specified name.
     * @param string $regionSlug restricts the search to volumes available in a specific region.
     *
     * @throws HttpException
     */
    public function deleteWithNameAndRegion($driveName, $regionSlug)
    {
        $this->adapter->delete(sprintf('%s/volumes?name=%s&region=%s', $this->endpoint, $driveName, $regionSlug));
    }
}