# AI Usage Notes

## 1. Where AI helped
- 从需求中快速提炼系统边界（做什么/不做什么）
- 生成初版架构方案候选（Kafka vs Redis Queue）
- 输出 Laravel 代码结构草案（API、Event/Listener、Notifier、迁移）
- 生成测试清单与 README 结构模板

## 2. Suggestions not adopted
- 未采纳“直接上 Kafka + 复杂 Outbox + 多层重试 Topic”作为 MVP 默认方案
  - 原因：作业时间预算内复杂度过高
- 未采纳“先做动态 DB 配置中心 + 管理后台”
  - 原因：功能跨度过大，不利于快速交付可验证 MVP
- 未采纳“先做 exactly-once”
  - 原因：实现成本高，收益与作业目标不匹配

## 3. Key decisions made manually
- 最终选择 `Laravel Queue + Redis` 作为默认中间件
- 明确采用 `event_type` 抽象，不同事件对应不同 Listener/Notifier
- 只做基础 payload 校验，保持 MVP 可落地
- 确定重试策略（`tries=6`, `backoff=[60,300,1800]`）
- 明确代码中保留 Kafka 演进点，但当前不接入
