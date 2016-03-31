<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchBundle\Tests\Functional\Result;

use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

class ObjectIteratorTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 'doc1',
                        'title' => 'Foo Product',
                        'related_categories' => [
                            [
                                'title' => 'Acme',
                            ],
                        ],
                    ],
                    [
                        '_id' => 'doc2',
                        'title' => 'Bar Product',
                        'related_categories' => [
                            [
                                'title' => 'Acme',
                                'color' => 'blue',
                            ],
                            [
                                'title' => 'Bar',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Iteration test.
     */
    public function testIteration()
    {
        /** @var Repository $repo */
        $repo = $this->getManager()->getRepository('AcmeBarBundle:Product');
        $match = new MatchAllQuery();
        $search = $repo->createSearch()->addQuery($match);
        $iterator = $repo->execute($search);

        $this->assertInstanceOf('ONGR\ElasticsearchBundle\Result\DocumentIterator', $iterator);

        foreach ($iterator as $document) {
            $categories = $document->getRelatedCategories();

            $this->assertInstanceOf(
                'ONGR\ElasticsearchBundle\Tests\app\fixture\Acme\BarBundle\Document\Product',
                $document
            );

            $this->assertInstanceOf('ONGR\ElasticsearchBundle\Result\ObjectIterator', $categories);
            $this->assertNotNull($categories[0]);
        }
    }
}