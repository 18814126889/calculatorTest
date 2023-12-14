<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeService extends GeneratorCommand
{

    /**
     * The console command name.
     * 控制台命令名。
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     * console命令说明。
     * @var string
     */
    protected $description = '生成service对象类';

    /**
     * The type of class being generated.
     * 生成的类的类型。
     * @var string
     */
    protected $type = 'Service';

    public function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $this->option('model');

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }

    /**
     * @description:Get the stub file for the generator.
     * @description:获取生成器的存根文件。
     * @return string
     * @Author:AKE
     * @Date:2022/5/24 10:50
     */
    protected function getStub()
    {
        return $this->option('model') ?
            $this->laravel->basePath('/stubs/service.plain.stub') :
            $this->laravel->basePath('/stubs/service.stub');
    }

    /**
     * @description:Get the default namespace for the class.
     * @description:获取默认命名空间
     * @param string $rootNamespace
     * @return string
     * @Author:AKE
     * @Date:2022/5/24 10:50
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }

    /**
     * @description:替换给定存根的模型
     * @param $stub
     * @param $model
     * @return array|string|string[]
     * @Author:AKE
     * @Date:2022/5/24 11:50
     */
    private function replaceModel($stub, $model)
    {
        $modelClass = $this->buildModel($model);

        $replace = [
            'DummyFullModelClass' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), $stub
        );
    }

    /**
     * @description:构建model名
     * @param $model
     * @return string
     * @Author:AKE
     * @Date:2022/5/24 11:47
     */
    private function buildModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('模型名称包含无效字符。');
        }
        return $this->qualifyModel($model);
    }

    /**
     * @description:添加参数
     * @return array[]
     * @Author:AKE
     * @Date:2022/5/24 13:04
     */
    protected function getOptions()
    {
        #第一个参数,为变量名,即 --model 调用或者 --model= 调用
        #第二个参数,为别名,即简写 -m 调用
        #第三个参数,Symfony\Component\Console\Input\InputOption 中的常量
        #第四个参数,为描述
        #第五个参数,为默认值 InputOption::VALUE_NONE 时必须为 null
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the service applies to.'],
        ];
    }
}
