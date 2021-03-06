# M2-ESIKAnalyzer

We describe here how to set up the Elesticsearch search field with Chinese Analyzer., i.e. [IK Analyzer](https://github.com/medcl/elasticsearch-analysis-ik/tree/master).  
Thanks to [Medcl](https://github.com/medcl), based on his efforts, we could analyze a document in Chinese characters with ES conveniently.

The other purpose is to document our journey of site search optimization and step to the customer preference result.

Since Magento version 2.4, Magento requires Elasticsearch to be the catalog search engine.  
In Elasticsearch, we can tune the search result via customize Analyzer, Dictionary, Search Query, and etc.


### Problem sample
* 枕頭 / 頭枕

## Overview of Search Term Tuning
![](https://github.com/MRLIVING/M2-ESIKAnalyzer/blob/main/doc/img/overview_search_term_tuning.PNG?raw=true)

## Elesticsearch Production Mode Configuration

### [Virtual memory](https://www.elastic.co/guide/en/elasticsearch/reference/current/vm-max-map-count.html)

* set `vm.max_map_count` in `/etc/sysctl.conf` to enable after rebooting
  ```
  vm.max_map_count = 262144
  ```

* , or `sysctl -w vm.max_map_count=262144` to enable this time only


### elesticsearch.yml
```
# ---------------------------------- Network -----------------------------------
# Set the bind address to a specific IP (IPv4 or IPv6):
network.host: 0.0.0.0
#
# --------------------------------- Discovery ----------------------------------
# Pass an initial list of hosts to perform discovery when this node is started:
# The default list of hosts is ["127.0.0.1", "[::1]"]
discovery.seed_hosts: [] 
#
# Bootstrap the cluster using an initial set of master-eligible nodes:
cluster.initial_master_nodes: []
```

### [Install IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/tree/v7.6.2#install)
Please watch the ES version and download the matching plugin version.
```
./bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v7.6.2/elasticsearch-analysis-ik-7.6.2.zip
```

### Restart Elasticsearch demon
```
kill ${ES_PROCESS_ID}
./bin/elasticsearch -d
```

### Dictionary Hot/Online update
TODO...


## Elesticsearch Client
### [Chrome extension - ElasticSearch Head](https://chrome.google.com/webstore/detail/elasticsearch-head/ffmkiejjmecolpfloofpjologoblkegm)
* Use `http://${ES_HOST}:9200/` to connect the ES service by default.

### Elesticsearch Top APIs 
* common URL  
  `http://${ES_HOST}:9200/${INDEX_NAME}/`

* [Get mapping API](https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-get-mapping.html#indices-get-mapping)  
  `GET _mapping`
  
* [Search API](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html#search-search)  
  * [query string syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html#query-string-syntax)  
  `GET _search?q=name:枕頭&_source=name&size=5&explain=true`

* [Term vectors API](https://www.elastic.co/guide/en/elasticsearch/reference/7.6/docs-termvectors.html#docs-termvectors)  
  `GET _termvectors/6640?fields=name`


## Magento 2 extension - ESIKAnalyzer, alter fields mapping
### Installation  
TODO...

### [Reindex](https://devdocs.magento.com/guides/v2.4/config-guide/cli/config-cli-subcommands-index.html#config-cli-subcommands-index-reindex)  
* Catalog Search  
  `./bin/magento index:reindex catalogsearch_fulltext`
  
  The upgrade command also trigger the search index to rebuild.  
  `./bin/magento setup:upgrade`

### Source for the [ES Field Analyzers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-analyzers.html#analysis-analyzers)
* `_search` [mapping source](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/AddDefaultSearchField.php#L29-L32)
  ```
  public function process(array $mapping): array
  {
      return [self::NAME => ['type' => 'text']] + $mapping;
  }
  ```

* `name`, `description` and the others [mapping source](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/Product/FieldProvider/StaticField.php#L202-L216)
  * [isNeedToAddCustomAnalyzer()](https://github.com/magento/magento2/blob/33242e4b19cf207d7b73f7791ef894b48bb41f8a/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/Product/FieldProvider/StaticField.php#L202) and [getCustomAnalyzer()](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/Product/FieldProvider/StaticField.php#L213) are invoked by [getField()](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/Product/FieldProvider/StaticField.php#L131). therefore we alter the mapping data by [intercepting plugin](https://www.mageplaza.com/magento-2-module-development/magento-2-plugin-interceptor.html) after the function call.   

Check [di.xml](https://github.com/MRLIVING/M2-ESIKAnalyzer/blob/main/etc/di.xml) for override and interception detail.

## Reference 
* [IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/tree/v7.6.2)
* [Information retrieval and Solr](https://1drv.ms/p/s!Ah4j_zHPfrc8hxY8cJwI8oIRri7r?e=bWOY6y)

