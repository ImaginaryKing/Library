<script src="{{JS}}angular.js"></script>
 <script src="{{JS}}ngStorage.js"></script>
<script src="{{JS}}angular-messages.js"></script>


<script>
var app = angular.module('academia', ['ngMessages']);
app.controller('prepareQuestions', function( $scope, $http) {
   $scope.savedQuestions = [];
    $scope.savedSeries =  [];
    $scope.total_exams = 0;
    $scope.total_questions = 0;

    $scope.lastPart = window.location.href.split("/").pop();

    if($scope.lastPart != 'add'){
        $http({
            method:"GET",
            url:'{{PREFIX}}'+'get_default_selectors/'+$scope.lastPart+'/examseries',
            dataType:"json",
            headers:{'Content-Type': 'application/x-www-form-urlencoded'}
        })
            .then(function (response) {
                $scope.branch = response.data.course_id.toString();
                $scope.getCategories($scope.branch);
                $scope.category = response.data.id;
            })
    }

    $scope.getCategories = function (id) {
        $http({
            method:"GET",
            url:'{{PREFIX}}'+'/get_categories/'+id+'/quizcategories',
            dataType:"json",
            headers:{'Content-Type': 'application/x-www-form-urlencoded'}
        })
            .then(function (response) {
                $scope.categories = response.data;
                $scope.category   = $scope.category.toString();
                console.log($scope.category,$scope.categories);
            })
    }
   
    $scope.initAngData = function(data) {
        
        if(data === undefined)
            return;
        $scope.removeAll();
        $scope.totalQuestions = 0;
        if(data=='')
        {
            $scope.examSeries   = [];
            return;
        }

        dta = data;
        $scope.savedSeries = dta.exams;
       $scope.totalQuestions = dta.total_questions;
        $scope.setItem('saved_series', $scope.savedSeries);
        $scope.setItem('total_exams', $scope.totalExams);
        $scope.setItem('total_questions', $scope.totalQuestions);
 
    }
    
     $scope.categoryChanged = function(selected_number) {      
        
        if(selected_number=='')
            selected_number = $scope.category_id;
        category_id = selected_number;
        if(category_id === undefined)
            return;
        route = '{{URL_EXAM_SERIES_GET_EXAMS}}';  
        data= {_method: 'post', '_token':$scope.getToken(), 'category_id': category_id};
         $scope.topics =[];
        $http.post(route, data).then(function(result, status) {
        $scope.categoryExams = [];
        $scope.categoryExams = result.data.exams;
        $scope.removeDuplicates();
        
        });
        }

        $scope.removeDuplicates = function(){
           
            if($scope.savedSeries.length<=0 )
                return;

             angular.forEach($scope.savedSeries,function(value,key){
                    
                    res = $scope.findIndexInData($scope.categoryExams, 'id', value.id);
                    if(res >= 0)
                    {
                         $scope.categoryExams.splice(res, 1);
                    }
                    
            });
        }
          
        $scope.addQuestion = function(exam) {
            
           var record = exam; 
            
              res = $scope.findIndexInData($scope.savedSeries, 'id', exam.id);
                    if(res == -1) {
                      $scope.savedSeries.push(record); 
                      if(isNaN($scope.totalQuestions))
                        $scope.totalQuestions = 0;
                       $scope.totalQuestions = parseInt($scope.totalQuestions) + parseInt(exam.total_questions); 
                      $scope.removeFromCategoryExams(exam);
                    }
                  else 
                    return;

           //Push record to storage
            $scope.setItem('saved_series', $scope.savedSeries);
            $scope.setItem('total_exams', $scope.savedSeries.length);
            $scope.setItem('total_questions', $scope.totalQuestions);
   
        }

        $scope.removeFromCategoryExams = function(item) { 
             var index = $scope.categoryExams.indexOf(item);
             $scope.categoryExams.splice(index, 1);     
        }

        $scope.addToCategoryExams = function(item) { 
           
             if($scope.categoryExams.length) {
                if($scope.categoryExams[0].category_id != item.category_id)
                    return;
                 res = $scope.findIndexInData($scope.savedSeries, 'id', item.id)
                    if(res == -1)
                      $scope.categoryExams.push(item);     
                
             }
        }

        $scope.findIndexInData =function (Array, property, action) {
          var result = -1;
          angular.forEach(Array, function(value, index) {
             if(value[property]==action){
                result=index;
             }
          });
          return result;
        }

        /**
         * Set item to local storage with the sent key and value
         * @param {[type]} $key   [localstorage key]
         * @param {[type]} $value [value]
         */
        $scope.setItem = function($key, $value){
            localStorage.setItem($key, JSON.stringify($value));
        }

        /**
         * Get item from local storage with the specified key
         * @param  {[type]} $key [localstorage key]
         * @return {[type]}      [description]
         */
        $scope.getItem = function($key){
            return JSON.parse(localStorage.getItem($key));
        }

        /**
         * Remove question with the sent id
         * @param  {[type]} id [description]
         * @return {[type]}    [description]
         */
         

    $scope.removeQuestion = function(record){
        
          $scope.savedSeries = $scope.savedSeries.filter(function(element){
            if(element.id != record.id)
              return element;
          });
           $scope.totalMarks = $scope.totalMarks - record.marks;
          $scope.setItem('saved_questions', $scope.savedSeries);
          $scope.addToCategoryExams(record);
        }

        $scope.removeAll = function(){
            $scope.savedSeries = [];
            $scope.totalQuestions       = 0;
            $scope.setItem('saved_questions', $scope.savedSeries);
            $scope.setItem('total_questions', $scope.totalQuestions);
            $scope.categoryChanged($scope.category_id);           
        }

     /**
     * Returns the token by fetching if from from form
     * 
     */
    $scope.getToken = function(){
      return  $('[name="_token"]').val();
    }  

}  );

app.filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                  //Also remove . and , so its gives a cleaner result.
                  if (value.charAt(lastspace-1) == '.' || value.charAt(lastspace-1) == ',') {
                    lastspace = lastspace - 1;
                  }
                  value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' ???');
        };
    });

 
</script>