# STEPS

## 我是怎么和 LLM 协作的（详细版）

### 1. 先对齐目标和约束（先聊清楚再写代码）
1. 我先给出目标：这是一个通知系统，存在多种业务事件，每种事件要走不同处理逻辑。
2. 我明确实现边界：MVP 优先，不追求功能齐全，要体现工程判断和取舍。
3. 我指定技术方向：`Laravel`，并要求支持异步、重试、失败可追踪。
4. 我要求文档能力：README 需要写设计思考，并附 Mermaid 架构图。

### 2. 用 LLM 做 plan，并多轮收敛
1. 第一轮先拿整体方案：架构、接口、数据模型、可靠性语义。
2. 第二轮我逐条纠偏：  
   - 不要只按供应商分发，要抽象“事件类型”。  
   - 每种事件对应不同处理方法。  
3. 第三轮我做关键取舍：  
   - 中间件用 `Redis Queue`，不实际接入 Horizon。  
   - 代码和文档里要保留“未来可升级 Kafka”的说明。  
4. 第四轮补交付要求：README 中必须体现设计思考和 Mermaid 图。

### 3. 我如何驱动 LLM 实施（从 plan 到代码）
1. 我下达执行指令：`Implement the plan.`  
2. 本机没有 PHP/Laravel 环境，我补充执行条件：使用 Docker 挂载当前目录开发。  
3. LLM 按这个条件完成：  
   - 初始化 Laravel 项目  
   - 实现 API、事件、监听器、Notifier、迁移、配置  
   - 写 README、AI_USAGE  
   - 加测试并执行验证

### 4. 关键合作方式（我负责决策，LLM负责落地）
1. 我负责的内容：
   - 决定方向和取舍（例如 Redis 而不是 Kafka、是否接 Horizon）。
   - 决定抽象方式（按事件类型路由，而不是按供应商硬编码）。
   - 决定交付要求（README 的思考深度、架构图要求）。
2. LLM 负责的内容：
   - 把方向转成可执行代码和文件结构。
   - 在实现中补全接口、状态流转、重试与失败处理。
   - 用测试验证实现是否符合预期。

### 5. 本次具体落地步骤
1. 结构设计：
   - `event_type -> Event/Listener -> Notifier`  
   - `NotificationDeliveryService` 管理投递状态和 attempt 记录。
2. 数据设计：
   - `notifications`（主状态）  
   - `notification_attempts`（每次调用）  
   - `failed_jobs`（最终失败沉淀）
3. 接口设计：
   - `POST /api/v1/notifications`（接收并入队）  
   - `GET /api/v1/notifications/{id}`（查询状态）
4. 可扩展设计：
   - 事件定义来源先走代码映射，后续可替换 DB 配置。  
   - 队列层先用 Redis，后续可替换 Kafka。

### 6. 最终验收（实现后必须做）
1. 迁移验证：`php artisan migrate:fresh --force`
2. 路由验证：`php artisan route:list --path=api`
3. 单测/集成测试：`php artisan test`
4. 文档验收：
   - README 是否写清“为什么这样设计”  
   - 是否包含 Mermaid 架构图和状态图  
   - 是否写明“为何没选 Kafka、未来如何演进”

### 7. 这套协作方法可复用
1. 先让 LLM 给方案，再由我做取舍，而不是一开始就让它直接写代码。
2. 每一轮都只改关键决策，避免反复推翻。
3. 只有当“目标、边界、取舍”稳定后，再进入实现。
4. 最后用“命令验证 + 测试结果 + 文档完整性”三件事收尾。
